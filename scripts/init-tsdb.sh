#!/bin/bash
#
# Script de Inicialização do TimescaleDB
# Automatiza setup completo do banco de dados para logs do Moodle
#

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
TSDB_HOST="${TSDB_HOST:-localhost}"
TSDB_PORT="${TSDB_PORT:-5433}"
TSDB_USER="${TSDB_USER:-moodleuser}"
TSDB_PASSWORD="${TSDB_PASSWORD:-moodlepass}"
TSDB_DATABASE="${TSDB_DATABASE:-moodle_logs_tsdb}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SCHEMA_FILE="$SCRIPT_DIR/../public/admin/tool/log/store/tsdb/db/timescaledb_schema.sql"

# Helper functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Docker is running
check_docker() {
    log_info "Checking Docker..."
    if ! docker ps &> /dev/null; then
        log_error "Docker is not running or you don't have permission"
        exit 1
    fi
    log_info "Docker is running ✓"
}

# Start TimescaleDB container if not running
start_timescaledb() {
    log_info "Checking TimescaleDB container..."

    if docker ps --format '{{.Names}}' | grep -q "^timescaledb$"; then
        log_info "TimescaleDB container is already running ✓"
        return 0
    fi

    if docker ps -a --format '{{.Names}}' | grep -q "^timescaledb$"; then
        log_info "Starting existing TimescaleDB container..."
        docker start timescaledb
    else
        log_info "TimescaleDB container not found"
        log_info "Please run: cd scripts && docker-compose up -d"
        exit 1
    fi

    # Wait for TimescaleDB to be ready
    log_info "Waiting for TimescaleDB to be ready..."
    for i in {1..30}; do
        if docker exec timescaledb pg_isready -U postgres &> /dev/null; then
            log_info "TimescaleDB is ready ✓"
            return 0
        fi
        echo -n "."
        sleep 1
    done

    log_error "TimescaleDB failed to start within 30 seconds"
    exit 1
}

# Create database if not exists
create_database() {
    log_info "Creating database '$TSDB_DATABASE'..."

    # Check if database exists
    if docker exec timescaledb psql -U postgres -lqt | cut -d \| -f 1 | grep -qw "$TSDB_DATABASE"; then
        log_warn "Database '$TSDB_DATABASE' already exists"
        read -p "Drop and recreate? (y/N): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            log_info "Dropping database..."
            docker exec timescaledb psql -U postgres -c "DROP DATABASE $TSDB_DATABASE;"
        else
            log_info "Skipping database creation"
            return 0
        fi
    fi

    docker exec timescaledb psql -U postgres -c "CREATE DATABASE $TSDB_DATABASE;"
    log_info "Database created ✓"
}

# Create user and grant permissions
setup_user() {
    log_info "Setting up user '$TSDB_USER'..."

    # Check if user exists
    if docker exec timescaledb psql -U postgres -tAc "SELECT 1 FROM pg_roles WHERE rolname='$TSDB_USER'" | grep -q 1; then
        log_warn "User '$TSDB_USER' already exists"
    else
        docker exec timescaledb psql -U postgres -c "CREATE USER $TSDB_USER WITH PASSWORD '$TSDB_PASSWORD';"
        log_info "User created ✓"
    fi

    # Grant permissions
    docker exec timescaledb psql -U postgres -c "GRANT ALL PRIVILEGES ON DATABASE $TSDB_DATABASE TO $TSDB_USER;"
    docker exec timescaledb psql -U postgres -d $TSDB_DATABASE -c "GRANT ALL ON SCHEMA public TO $TSDB_USER;"
    log_info "Permissions granted ✓"
}

# Initialize schema
init_schema() {
    log_info "Initializing schema..."

    if [ ! -f "$SCHEMA_FILE" ]; then
        log_error "Schema file not found: $SCHEMA_FILE"
        exit 1
    fi

    log_info "Executing schema SQL..."
    docker exec -i timescaledb psql -U $TSDB_USER -d $TSDB_DATABASE < "$SCHEMA_FILE"

    log_info "Schema initialized ✓"
}

# Verify installation
verify_setup() {
    log_info "Verifying setup..."

    # Check TimescaleDB extension
    if docker exec timescaledb psql -U $TSDB_USER -d $TSDB_DATABASE -tAc "SELECT extversion FROM pg_extension WHERE extname='timescaledb'" | grep -q "^2"; then
        log_info "TimescaleDB extension: $(docker exec timescaledb psql -U $TSDB_USER -d $TSDB_DATABASE -tAc "SELECT extversion FROM pg_extension WHERE extname='timescaledb'") ✓"
    else
        log_error "TimescaleDB extension not found"
        exit 1
    fi

    # Check hypertable
    if docker exec timescaledb psql -U $TSDB_USER -d $TSDB_DATABASE -tAc "SELECT * FROM timescaledb_information.hypertables WHERE hypertable_name='moodle_events'" | grep -q "moodle_events"; then
        log_info "Hypertable 'moodle_events' exists ✓"
    else
        log_error "Hypertable 'moodle_events' not found"
        exit 1
    fi

    # Test insert
    docker exec timescaledb psql -U $TSDB_USER -d $TSDB_DATABASE -c "
        INSERT INTO moodle_events (time, eventname, component, action, target)
        VALUES (NOW(), 'test_event', 'test', 'test', 'test');
        DELETE FROM moodle_events WHERE eventname = 'test_event';
    " &> /dev/null

    log_info "Insert test passed ✓"
}

# Print connection info
print_info() {
    echo ""
    echo "════════════════════════════════════════════════════════"
    echo "  TimescaleDB Setup Complete!"
    echo "════════════════════════════════════════════════════════"
    echo ""
    echo "Connection Info:"
    echo "  Host:     $TSDB_HOST"
    echo "  Port:     $TSDB_PORT"
    echo "  Database: $TSDB_DATABASE"
    echo "  User:     $TSDB_USER"
    echo "  Password: $TSDB_PASSWORD"
    echo ""
    echo "Connect:"
    echo "  psql -h $TSDB_HOST -p $TSDB_PORT -U $TSDB_USER -d $TSDB_DATABASE"
    echo ""
    echo "Moodle Plugin Configuration:"
    echo "  Site Administration → Plugins → Logging → TSDB Log Store"
    echo ""
    echo "════════════════════════════════════════════════════════"
}

# Main execution
main() {
    echo ""
    echo "═══════════════════════════════════════════"
    echo "  TimescaleDB Initialization Script"
    echo "═══════════════════════════════════════════"
    echo ""

    check_docker
    start_timescaledb
    create_database
    setup_user
    init_schema
    verify_setup
    print_info

    log_info "All done! 🎉"
}

main "$@"
