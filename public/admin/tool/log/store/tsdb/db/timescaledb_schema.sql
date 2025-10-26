-- ============================================================
-- TimescaleDB Schema for Moodle Events Logging
-- ============================================================
--
-- This script creates the hypertable and indexes for storing
-- Moodle events in TimescaleDB with optimal performance.
--
-- Usage:
--   psql -h localhost -p 5433 -U moodleuser -d moodle_logs_tsdb -f timescaledb_schema.sql
--
-- Author: TCC Moodle TSDB Plugin
-- Version: 1.0.0
-- Date: 2025-01-25
-- ============================================================

-- Drop table if exists (para desenvolvimento)
-- DROP TABLE IF EXISTS moodle_events CASCADE;

-- ============================================================
-- Main Events Table
-- ============================================================

CREATE TABLE IF NOT EXISTS moodle_events (
    -- Timestamp (primary dimension for TimescaleDB)
    time TIMESTAMPTZ NOT NULL,

    -- Event identification
    eventname TEXT NOT NULL,          -- e.g., '\core\event\user_loggedin'
    component TEXT,                    -- e.g., 'core', 'mod_quiz', 'block_navigation'
    action TEXT,                       -- e.g., 'viewed', 'created', 'submitted', 'deleted'
    target TEXT,                       -- e.g., 'user', 'course', 'course_module'

    -- Event metadata
    crud CHAR(1),                      -- 'c', 'r', 'u', 'd'
    edulevel SMALLINT,                 -- 0 (OTHER), 1 (TEACHING), 2 (PARTICIPATING)
    anonymous SMALLINT DEFAULT 0,      -- 0 or 1

    -- Context
    courseid INTEGER,                  -- Course ID (0 if system-level)
    contextid INTEGER,                 -- Context ID
    contextlevel SMALLINT,             -- 10 (SYSTEM), 50 (COURSE), 70 (MODULE), etc.
    contextinstanceid INTEGER,         -- Instance ID of the context

    -- Actors and objects
    userid INTEGER,                    -- User who performed the action
    relateduserid INTEGER,             -- Related user (optional)
    realuserid INTEGER,                -- Real user (in case of loginas)
    objectid INTEGER,                  -- ID of the affected object
    objecttable TEXT,                  -- Table name of the object

    -- Additional info
    ip INET,                           -- IP address
    origin TEXT,                       -- 'web', 'ws', 'cli', 'restore'
    other JSONB                        -- Additional event-specific data
);

-- ============================================================
-- Convert to Hypertable
-- ============================================================

-- Create hypertable partitioned by time (1 day chunks)
SELECT create_hypertable(
    'moodle_events',
    'time',
    chunk_time_interval => INTERVAL '1 day',
    if_not_exists => TRUE
);

-- ============================================================
-- Indexes for Query Performance
-- ============================================================

-- Time-based queries (most common)
CREATE INDEX IF NOT EXISTS idx_moodle_events_time
    ON moodle_events (time DESC);

-- Event type queries
CREATE INDEX IF NOT EXISTS idx_moodle_events_eventname
    ON moodle_events (time DESC, eventname);

CREATE INDEX IF NOT EXISTS idx_moodle_events_component
    ON moodle_events (time DESC, component);

CREATE INDEX IF NOT EXISTS idx_moodle_events_action
    ON moodle_events (time DESC, action);

-- User activity queries
CREATE INDEX IF NOT EXISTS idx_moodle_events_userid
    ON moodle_events (time DESC, userid);

CREATE INDEX IF NOT EXISTS idx_moodle_events_realuserid
    ON moodle_events (time DESC, realuserid)
    WHERE realuserid IS NOT NULL;

-- Course activity queries
CREATE INDEX IF NOT EXISTS idx_moodle_events_courseid
    ON moodle_events (time DESC, courseid)
    WHERE courseid > 0;

-- Context queries
CREATE INDEX IF NOT EXISTS idx_moodle_events_contextid
    ON moodle_events (time DESC, contextid);

-- CRUD type queries
CREATE INDEX IF NOT EXISTS idx_moodle_events_crud
    ON moodle_events (crud, time DESC);

-- Educational level queries
CREATE INDEX IF NOT EXISTS idx_moodle_events_edulevel
    ON moodle_events (edulevel, time DESC);

-- Combined index for common query patterns
CREATE INDEX IF NOT EXISTS idx_moodle_events_component_action
    ON moodle_events (component, action, time DESC);

-- JSONB index for 'other' field queries
CREATE INDEX IF NOT EXISTS idx_moodle_events_other_gin
    ON moodle_events USING GIN (other);

-- ============================================================
-- Compression Policy
-- ============================================================

-- Enable compression
ALTER TABLE moodle_events SET (
    timescaledb.compress,
    timescaledb.compress_segmentby = 'component, action, edulevel',
    timescaledb.compress_orderby = 'time DESC, userid'
);

-- Compress chunks older than 7 days
SELECT add_compression_policy('moodle_events', INTERVAL '7 days');

-- ============================================================
-- Retention Policy
-- ============================================================

-- Delete data older than 1 year (adjust as needed)
SELECT add_retention_policy('moodle_events', INTERVAL '1 year');

-- ============================================================
-- Continuous Aggregates (Pre-computed views)
-- ============================================================

-- Hourly event counts by component
CREATE MATERIALIZED VIEW IF NOT EXISTS moodle_events_hourly
WITH (timescaledb.continuous) AS
SELECT
    time_bucket('1 hour', time) AS bucket,
    component,
    action,
    edulevel,
    COUNT(*) as event_count,
    COUNT(DISTINCT userid) as unique_users,
    COUNT(DISTINCT courseid) as unique_courses
FROM moodle_events
WHERE courseid > 0
GROUP BY bucket, component, action, edulevel
WITH NO DATA;

-- Refresh policy for continuous aggregate
SELECT add_continuous_aggregate_policy('moodle_events_hourly',
    start_offset => INTERVAL '3 hours',
    end_offset => INTERVAL '1 hour',
    schedule_interval => INTERVAL '1 hour'
);

-- Daily event counts
CREATE MATERIALIZED VIEW IF NOT EXISTS moodle_events_daily
WITH (timescaledb.continuous) AS
SELECT
    time_bucket('1 day', time) AS bucket,
    component,
    COUNT(*) as event_count,
    COUNT(DISTINCT userid) as unique_users,
    COUNT(DISTINCT courseid) as unique_courses
FROM moodle_events
WHERE courseid > 0
GROUP BY bucket, component
WITH NO DATA;

-- Refresh policy for daily aggregate
SELECT add_continuous_aggregate_policy('moodle_events_daily',
    start_offset => INTERVAL '7 days',
    end_offset => INTERVAL '1 day',
    schedule_interval => INTERVAL '1 day'
);

-- ============================================================
-- Helper Functions
-- ============================================================

-- Function to get events per hour for the last N hours
CREATE OR REPLACE FUNCTION get_events_per_hour(hours INTEGER DEFAULT 24)
RETURNS TABLE (
    hour TIMESTAMPTZ,
    count BIGINT
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        time_bucket('1 hour', time) AS hour,
        COUNT(*) AS count
    FROM moodle_events
    WHERE time > NOW() - (hours || ' hours')::INTERVAL
    GROUP BY hour
    ORDER BY hour DESC;
END;
$$ LANGUAGE plpgsql;

-- Function to get top N most active users
CREATE OR REPLACE FUNCTION get_top_active_users(
    hours INTEGER DEFAULT 24,
    limit_count INTEGER DEFAULT 10
)
RETURNS TABLE (
    userid INTEGER,
    event_count BIGINT,
    unique_actions BIGINT
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        e.userid,
        COUNT(*) AS event_count,
        COUNT(DISTINCT e.action) AS unique_actions
    FROM moodle_events e
    WHERE e.time > NOW() - (hours || ' hours')::INTERVAL
      AND e.userid > 0
    GROUP BY e.userid
    ORDER BY event_count DESC
    LIMIT limit_count;
END;
$$ LANGUAGE plpgsql;

-- Function to get event statistics
CREATE OR REPLACE FUNCTION get_event_statistics(
    start_time TIMESTAMPTZ DEFAULT NOW() - INTERVAL '24 hours',
    end_time TIMESTAMPTZ DEFAULT NOW()
)
RETURNS TABLE (
    total_events BIGINT,
    unique_users BIGINT,
    unique_courses BIGINT,
    unique_components BIGINT,
    avg_events_per_minute NUMERIC
) AS $$
BEGIN
    RETURN QUERY
    SELECT
        COUNT(*) AS total_events,
        COUNT(DISTINCT userid) AS unique_users,
        COUNT(DISTINCT courseid) FILTER (WHERE courseid > 0) AS unique_courses,
        COUNT(DISTINCT component) AS unique_components,
        ROUND(COUNT(*)::NUMERIC / EXTRACT(EPOCH FROM (end_time - start_time)) * 60, 2) AS avg_events_per_minute
    FROM moodle_events
    WHERE time BETWEEN start_time AND end_time;
END;
$$ LANGUAGE plpgsql;

-- ============================================================
-- Verify Installation
-- ============================================================

-- Check hypertable was created
DO $$
BEGIN
    IF EXISTS (
        SELECT 1 FROM timescaledb_information.hypertables
        WHERE hypertable_name = 'moodle_events'
    ) THEN
        RAISE NOTICE 'SUCCESS: Hypertable "moodle_events" created successfully!';
    ELSE
        RAISE EXCEPTION 'ERROR: Hypertable "moodle_events" was not created!';
    END IF;
END $$;

-- Display table information
SELECT
    hypertable_schema,
    hypertable_name,
    num_dimensions,
    num_chunks,
    compression_enabled,
    total_table_size
FROM timescaledb_information.hypertables
WHERE hypertable_name = 'moodle_events';

-- Display compression stats
SELECT
    chunk_name,
    compression_status,
    before_compression_total_bytes,
    after_compression_total_bytes,
    CASE
        WHEN before_compression_total_bytes > 0 THEN
            ROUND(100.0 * (1 - after_compression_total_bytes::NUMERIC / before_compression_total_bytes), 2)
        ELSE 0
    END AS compression_ratio_pct
FROM timescaledb_information.chunks
WHERE hypertable_name = 'moodle_events'
  AND compression_status = 'Compressed'
ORDER BY chunk_name DESC
LIMIT 10;

-- Display indexes
SELECT
    indexname,
    indexdef
FROM pg_indexes
WHERE tablename = 'moodle_events'
ORDER BY indexname;

-- Grant permissions (if needed)
-- GRANT SELECT, INSERT ON moodle_events TO moodleuser;
-- GRANT SELECT ON moodle_events_hourly TO moodleuser;
-- GRANT SELECT ON moodle_events_daily TO moodleuser;

RAISE NOTICE '';
RAISE NOTICE '============================================================';
RAISE NOTICE 'TimescaleDB Schema Setup Complete!';
RAISE NOTICE '============================================================';
RAISE NOTICE 'Next steps:';
RAISE NOTICE '  1. Test insertion: INSERT INTO moodle_events (time, eventname, component, action, userid) VALUES (NOW(), ''\core\event\test'', ''core'', ''test'', 1);';
RAISE NOTICE '  2. Query data: SELECT * FROM moodle_events ORDER BY time DESC LIMIT 10;';
RAISE NOTICE '  3. Check stats: SELECT * FROM get_event_statistics();';
RAISE NOTICE '  4. Configure Moodle plugin to connect to this database';
RAISE NOTICE '============================================================';
