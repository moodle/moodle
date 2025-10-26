#!/bin/bash
#
# Environment Checker for Moodle TCC Project
# Verifies all components are properly configured
#

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Counters
CHECKS_PASSED=0
CHECKS_FAILED=0
CHECKS_WARNING=0

# Helper functions
check_pass() {
    echo -e "${GREEN}✓${NC} $1"
    ((CHECKS_PASSED++))
}

check_fail() {
    echo -e "${RED}✗${NC} $1"
    ((CHECKS_FAILED++))
}

check_warn() {
    echo -e "${YELLOW}⚠${NC} $1"
    ((CHECKS_WARNING++))
}

section() {
    echo ""
    echo -e "${BLUE}═══ $1 ═══${NC}"
}

# PHP Checks
check_php() {
    section "PHP Environment"

    if command -v php &> /dev/null; then
        PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
        if (( $(echo "$PHP_VERSION >= 8.2" | bc -l) )); then
            check_pass "PHP $PHP_VERSION installed"
        else
            check_fail "PHP $PHP_VERSION installed (requires >= 8.2)"
        fi
    else
        check_fail "PHP not found"
        return
    fi

    # Check extensions
    REQUIRED_EXTS=("pgsql" "mbstring" "curl" "xml" "zip" "gd" "intl" "json")
    for ext in "${REQUIRED_EXTS[@]}"; do
        if php -m | grep -qi "^$ext$"; then
            check_pass "PHP extension: $ext"
        else
            check_fail "PHP extension missing: $ext"
        fi
    done
}

# PostgreSQL Checks
check_postgresql() {
    section "PostgreSQL (Main Database)"

    if command -v psql &> /dev/null; then
        PG_VERSION=$(psql --version | awk '{print $3}' | cut -d "." -f 1)
        if [ "$PG_VERSION" -ge 14 ]; then
            check_pass "PostgreSQL $PG_VERSION installed"
        else
            check_warn "PostgreSQL $PG_VERSION (recommended >= 14)"
        fi
    else
        check_warn "PostgreSQL client not found (may not be needed if using Docker)"
    fi

    # Try to connect to local PostgreSQL
    if pg_isready -h localhost -p 5432 &> /dev/null; then
        check_pass "PostgreSQL server running on localhost:5432"
    else
        check_warn "PostgreSQL server not accessible on localhost:5432"
    fi
}

# TimescaleDB Checks
check_timescaledb() {
    section "TimescaleDB"

    # Check if Docker container is running
    if docker ps --format '{{.Names}}' 2>/dev/null | grep -q "^timescaledb$"; then
        check_pass "TimescaleDB container running"

        # Check if accessible
        if docker exec timescaledb pg_isready -U postgres &> /dev/null; then
            check_pass "TimescaleDB server accessible"

            # Check TimescaleDB extension
            TSDB_VERSION=$(docker exec timescaledb psql -U postgres -tAc "SELECT extversion FROM pg_extension WHERE extname='timescaledb'" 2>/dev/null || echo "")
            if [ -n "$TSDB_VERSION" ]; then
                check_pass "TimescaleDB extension version: $TSDB_VERSION"
            else
                check_fail "TimescaleDB extension not installed"
            fi

            # Check database exists
            if docker exec timescaledb psql -U postgres -lqt 2>/dev/null | cut -d \| -f 1 | grep -qw "moodle_logs_tsdb"; then
                check_pass "Database 'moodle_logs_tsdb' exists"

                # Check hypertable
                if docker exec timescaledb psql -U postgres -d moodle_logs_tsdb -tAc "SELECT * FROM timescaledb_information.hypertables WHERE hypertable_name='moodle_events'" 2>/dev/null | grep -q "moodle_events"; then
                    check_pass "Hypertable 'moodle_events' configured"
                else
                    check_fail "Hypertable 'moodle_events' not found - run init-tsdb.sh"
                fi
            else
                check_fail "Database 'moodle_logs_tsdb' not found - run init-tsdb.sh"
            fi
        else
            check_fail "TimescaleDB server not responding"
        fi
    else
        check_fail "TimescaleDB container not running - run: cd scripts && docker-compose up -d"
    fi
}

# Node.js Checks
check_nodejs() {
    section "Node.js"

    if command -v node &> /dev/null; then
        NODE_VERSION=$(node -v | cut -d "v" -f 2 | cut -d "." -f 1)
        if [ "$NODE_VERSION" -ge 22 ]; then
            check_pass "Node.js v$(node -v | cut -d "v" -f 2) installed"
        else
            check_warn "Node.js v$(node -v | cut -d "v" -f 2) (recommended >= v22)"
        fi
    else
        check_fail "Node.js not found"
    fi

    if command -v npm &> /dev/null; then
        check_pass "npm $(npm -v) installed"
    else
        check_fail "npm not found"
    fi
}

# Composer Checks
check_composer() {
    section "Composer"

    if command -v composer &> /dev/null; then
        check_pass "Composer $(composer --version 2>/dev/null | awk '{print $3}') installed"
    else
        check_fail "Composer not found"
    fi
}

# Docker Checks
check_docker() {
    section "Docker"

    if command -v docker &> /dev/null; then
        check_pass "Docker $(docker --version | awk '{print $3}' | tr -d ',') installed"

        if docker ps &> /dev/null; then
            check_pass "Docker daemon running"
        else
            check_fail "Docker daemon not running or permission denied"
        fi
    else
        check_fail "Docker not found"
    fi
}

# Moodle Checks
check_moodle() {
    section "Moodle Installation"

    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    MOODLE_ROOT="$SCRIPT_DIR/../public"

    if [ -f "$MOODLE_ROOT/version.php" ]; then
        check_pass "Moodle found at: $MOODLE_ROOT"

        # Check if config.php exists
        if [ -f "$SCRIPT_DIR/../config.php" ]; then
            check_pass "config.php exists"
        else
            check_warn "config.php not found - Moodle not configured yet"
        fi

        # Check moodledata directory
        if [ -d "$HOME/moodledata" ]; then
            check_pass "moodledata directory exists"
        else
            check_warn "moodledata directory not found at $HOME/moodledata"
        fi
    else
        check_fail "Moodle not found - version.php missing"
    fi
}

# Plugin Checks
check_plugin() {
    section "logstore_tsdb Plugin"

    PLUGIN_DIR="$SCRIPT_DIR/../public/admin/tool/log/store/tsdb"

    if [ -d "$PLUGIN_DIR" ]; then
        check_pass "Plugin directory exists"

        # Check required files
        if [ -f "$PLUGIN_DIR/version.php" ]; then
            check_pass "version.php found"
        else
            check_fail "version.php missing"
        fi

        if [ -f "$PLUGIN_DIR/classes/log/store.php" ]; then
            check_pass "store.php found"
        else
            check_fail "store.php missing"
        fi

        if [ -f "$PLUGIN_DIR/classes/client/timescaledb_client.php" ]; then
            check_pass "timescaledb_client.php found"
        else
            check_fail "timescaledb_client.php missing"
        fi

        if [ -f "$PLUGIN_DIR/db/timescaledb_schema.sql" ]; then
            check_pass "schema SQL found"
        else
            check_fail "schema SQL missing"
        fi
    else
        check_fail "Plugin not found at: $PLUGIN_DIR"
    fi
}

# Python Checks
check_python() {
    section "Python (for Simulation)"

    if command -v python3 &> /dev/null; then
        PY_VERSION=$(python3 --version | awk '{print $2}')
        check_pass "Python $PY_VERSION installed"

        # Check pip
        if python3 -m pip --version &> /dev/null; then
            check_pass "pip installed"

            # Check required packages
            SIM_DIR="$SCRIPT_DIR/simulation"
            if [ -f "$SIM_DIR/requirements.txt" ]; then
                if python3 -m pip show requests &> /dev/null; then
                    check_pass "Python package: requests"
                else
                    check_warn "Python package missing: requests (run: pip install -r requirements.txt)"
                fi

                if python3 -m pip show faker &> /dev/null; then
                    check_pass "Python package: faker"
                else
                    check_warn "Python package missing: faker"
                fi
            fi
        else
            check_warn "pip not found"
        fi
    else
        check_warn "Python3 not found (needed for simulation scripts)"
    fi
}

# Print Summary
print_summary() {
    echo ""
    echo "═══════════════════════════════════════════"
    echo "  Environment Check Summary"
    echo "═══════════════════════════════════════════"
    echo ""
    echo -e "  ${GREEN}Passed:${NC}  $CHECKS_PASSED"
    echo -e "  ${YELLOW}Warnings:${NC} $CHECKS_WARNING"
    echo -e "  ${RED}Failed:${NC}  $CHECKS_FAILED"
    echo ""

    if [ $CHECKS_FAILED -eq 0 ]; then
        echo -e "${GREEN}✓ Environment is ready!${NC}"
        echo ""
        echo "Next steps:"
        echo "  1. Configure Moodle: cp config-dist.php config.php"
        echo "  2. Install Moodle: php public/admin/cli/install_database.php"
        echo "  3. Configure Web Services (see docs/API-MOODLE.md)"
        echo "  4. Run simulation: cd scripts/simulation && python generate_load.py"
    else
        echo -e "${RED}✗ Please fix the failed checks above${NC}"
        echo ""
        echo "Common fixes:"
        echo "  - Install missing PHP extensions: sudo apt-get install php-<extension>"
        echo "  - Start TimescaleDB: cd scripts && docker-compose up -d"
        echo "  - Initialize TSDB: ./scripts/init-tsdb.sh"
    fi

    echo ""
}

# Main
main() {
    echo ""
    echo "═══════════════════════════════════════════"
    echo "  Moodle TCC Environment Checker"
    echo "═══════════════════════════════════════════"

    check_php
    check_postgresql
    check_timescaledb
    check_nodejs
    check_composer
    check_docker
    check_moodle
    check_plugin
    check_python

    print_summary
}

main "$@"
