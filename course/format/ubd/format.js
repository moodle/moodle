/**
 * UbD course format JavaScript functionality
 *
 * @package    format_ubd
 * @copyright  2025 Moodle Evolved Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// UbD Format JavaScript Module
(function() {
    'use strict';

    /**
     * Initialize UbD format functionality
     */
    function initUbDFormat() {
        console.log('UbD Format initialized');
        
        // Add auto-save functionality (every 30 seconds)
        setInterval(function() {
            autoSaveUbDPlan();
        }, 30000);
        
        // Add change listeners to all UbD textareas
        var ubdTextareas = document.querySelectorAll('.ubd-field textarea');
        ubdTextareas.forEach(function(textarea) {
            textarea.addEventListener('input', function() {
                // Mark as changed
                textarea.classList.add('ubd-changed');
            });
        });
    }

    /**
     * Auto-save UbD plan data
     */
    function autoSaveUbDPlan() {
        var changedTextareas = document.querySelectorAll('.ubd-field textarea.ubd-changed');
        if (changedTextareas.length > 0) {
            console.log('Auto-saving UbD plan...');
            // Remove changed class
            changedTextareas.forEach(function(textarea) {
                textarea.classList.remove('ubd-changed');
            });
            // Here we would implement actual AJAX save
        }
    }

    /**
     * Save UbD plan via AJAX
     */
    function saveUbDPlan() {
        console.log('Saving UbD plan...');

        // Get course ID from the page
        var courseId = M.cfg.courseId || document.querySelector('[data-courseid]')?.getAttribute('data-courseid');
        if (!courseId) {
            console.error('Course ID not found');
            return;
        }

        // Collect all UbD data
        var ubdData = {
            courseid: courseId,
            action: 'save_ubd_plan',
            sesskey: M.cfg.sesskey,
            ubd_stage1_enduring: document.getElementById('ubd_enduring')?.value || '',
            ubd_stage1_questions: document.getElementById('ubd_questions')?.value || '',
            ubd_stage1_knowledge: document.getElementById('ubd_knowledge')?.value || '',
            ubd_stage2_performance: document.getElementById('ubd_performance')?.value || '',
            ubd_stage2_evidence: document.getElementById('ubd_evidence')?.value || '',
            ubd_stage3_activities: document.getElementById('ubd_activities')?.value || ''
        };

        // Create form data
        var formData = new FormData();
        for (var key in ubdData) {
            formData.append(key, ubdData[key]);
        }

        // Send AJAX request
        fetch(M.cfg.wwwroot + '/course/format/ubd/ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('UbD plan saved successfully');
                // Show success message
                showNotification('UbD plan saved successfully!', 'success');
            } else {
                console.error('Error saving UbD plan:', data.message);
                showNotification('Error saving UbD plan: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('AJAX error:', error);
            showNotification('Network error occurred while saving', 'error');
        });
    }

    /**
     * Show notification message
     */
    function showNotification(message, type) {
        // Create notification element
        var notification = document.createElement('div');
        notification.className = 'ubd-notification ubd-notification-' + type;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            z-index: 9999;
            background-color: ${type === 'success' ? '#28a745' : '#dc3545'};
        `;

        document.body.appendChild(notification);

        // Remove after 3 seconds
        setTimeout(function() {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUbDFormat);
    } else {
        initUbDFormat();
    }

})();
