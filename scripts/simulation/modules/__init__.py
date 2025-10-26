"""
Moodle Load Simulation Modules.

This package contains modules for simulating realistic user interactions
with a Moodle platform to generate log data for testing.
"""

__version__ = '1.0.0'
__author__ = 'TCC Project'

from . import moodle_api
from . import users
from . import courses
from . import activities
from . import interactions

__all__ = [
    'moodle_api',
    'users',
    'courses',
    'activities',
    'interactions',
]
