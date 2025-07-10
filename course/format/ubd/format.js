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

    var isAutoSaving = false;
    var hasUnsavedChanges = false;
    var autoSaveInterval;

    /**
     * Initialize UbD format functionality
     */
    function initUbDFormat() {
        console.log('UbD Format initialized');

        // Initialize UI components
        initializeUI();

        // Add auto-save functionality (every 30 seconds)
        autoSaveInterval = setInterval(function() {
            autoSaveUbDPlan();
        }, 30000);

        // Add change listeners to all UbD textareas
        var ubdTextareas = document.querySelectorAll('.ubd-field textarea');
        ubdTextareas.forEach(function(textarea) {
            textarea.addEventListener('input', function() {
                // Mark as changed
                textarea.classList.add('ubd-changed');
                hasUnsavedChanges = true;
                updateSaveButtonState();

                // Add visual indicator for unsaved changes
                addUnsavedIndicator(textarea);
            });

            // Add character count
            addCharacterCount(textarea);
        });

        // Warn user about unsaved changes when leaving page
        window.addEventListener('beforeunload', function(e) {
            if (hasUnsavedChanges && !isAutoSaving) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
    }

    /**
     * Initialize UI components
     */
    function initializeUI() {
        // Add expand/collapse functionality for stages
        var stageHeaders = document.querySelectorAll('.ubd-stage h3');
        stageHeaders.forEach(function(header) {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                toggleStage(header.parentElement);
            });

            // Add expand/collapse icon
            var icon = document.createElement('span');
            icon.className = 'ubd-toggle-icon';
            icon.innerHTML = '▼';
            icon.style.marginLeft = '10px';
            icon.style.fontSize = '0.8em';
            header.appendChild(icon);
        });

        // Add template buttons
        addTemplateButtons();

        // Add export button
        addExportButton();
    }

    /**
     * Toggle stage visibility
     */
    function toggleStage(stageElement) {
        var content = stageElement.querySelector('.ubd-stage-content');
        var icon = stageElement.querySelector('.ubd-toggle-icon');

        if (!content) {
            // Create content wrapper if it doesn't exist
            content = document.createElement('div');
            content.className = 'ubd-stage-content';
            var children = Array.from(stageElement.children);
            children.forEach(function(child) {
                if (child.tagName !== 'H3') {
                    content.appendChild(child);
                }
            });
            stageElement.appendChild(content);
        }

        if (content.style.display === 'none') {
            content.style.display = 'block';
            icon.innerHTML = '▼';
            stageElement.classList.remove('collapsed');
        } else {
            content.style.display = 'none';
            icon.innerHTML = '▶';
            stageElement.classList.add('collapsed');
        }
    }

    /**
     * Auto-save UbD plan data
     */
    function autoSaveUbDPlan() {
        var changedTextareas = document.querySelectorAll('.ubd-field textarea.ubd-changed');
        if (changedTextareas.length > 0 && !isAutoSaving) {
            console.log('Auto-saving UbD plan...');
            isAutoSaving = true;
            showAutoSaveIndicator();

            saveUbDPlan(true).then(function() {
                // Remove changed class
                changedTextareas.forEach(function(textarea) {
                    textarea.classList.remove('ubd-changed');
                    removeUnsavedIndicator(textarea);
                });
                hasUnsavedChanges = false;
                updateSaveButtonState();
                hideAutoSaveIndicator();
                isAutoSaving = false;
            }).catch(function() {
                hideAutoSaveIndicator();
                isAutoSaving = false;
            });
        }
    }

    /**
     * Add character count to textarea
     */
    function addCharacterCount(textarea) {
        var countDiv = document.createElement('div');
        countDiv.className = 'ubd-char-count';
        countDiv.style.cssText = 'font-size: 0.8em; color: #666; text-align: right; margin-top: 5px;';

        function updateCount() {
            var count = textarea.value.length;
            countDiv.textContent = count + ' characters';
            if (count > 1000) {
                countDiv.style.color = '#d63384';
            } else if (count > 500) {
                countDiv.style.color = '#fd7e14';
            } else {
                countDiv.style.color = '#666';
            }
        }

        textarea.addEventListener('input', updateCount);
        updateCount();

        textarea.parentElement.appendChild(countDiv);
    }

    /**
     * Add unsaved indicator to textarea
     */
    function addUnsavedIndicator(textarea) {
        if (!textarea.parentElement.querySelector('.ubd-unsaved-indicator')) {
            var indicator = document.createElement('span');
            indicator.className = 'ubd-unsaved-indicator';
            indicator.innerHTML = '● ';
            indicator.style.cssText = 'color: #d63384; font-weight: bold; margin-left: 5px;';
            indicator.title = 'Unsaved changes';

            var label = textarea.parentElement.querySelector('label');
            if (label) {
                label.appendChild(indicator);
            }
        }
    }

    /**
     * Remove unsaved indicator from textarea
     */
    function removeUnsavedIndicator(textarea) {
        var indicator = textarea.parentElement.querySelector('.ubd-unsaved-indicator');
        if (indicator) {
            indicator.remove();
        }
    }

    /**
     * Update save button state
     */
    function updateSaveButtonState() {
        var saveButton = document.getElementById('save-ubd-plan');
        if (saveButton) {
            if (hasUnsavedChanges) {
                saveButton.textContent = 'Save Changes *';
                saveButton.classList.add('btn-warning');
                saveButton.classList.remove('btn-primary');
            } else {
                saveButton.textContent = 'Save UbD Plan';
                saveButton.classList.add('btn-primary');
                saveButton.classList.remove('btn-warning');
            }
        }
    }

    /**
     * Show auto-save indicator
     */
    function showAutoSaveIndicator() {
        var indicator = document.getElementById('auto-save-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'auto-save-indicator';
            indicator.innerHTML = '💾 Auto-saving...';
            indicator.style.cssText = `
                position: fixed;
                top: 10px;
                right: 10px;
                background: #17a2b8;
                color: white;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 0.9em;
                z-index: 10000;
            `;
            document.body.appendChild(indicator);
        }
        indicator.style.display = 'block';
    }

    /**
     * Hide auto-save indicator
     */
    function hideAutoSaveIndicator() {
        var indicator = document.getElementById('auto-save-indicator');
        if (indicator) {
            setTimeout(function() {
                indicator.style.display = 'none';
            }, 1000);
        }
    }

    /**
     * Add template buttons
     */
    function addTemplateButtons() {
        var templateSection = document.createElement('div');
        templateSection.className = 'ubd-templates';
        templateSection.innerHTML = `
            <h4>📋 UbD Templates</h4>
            <div class="ubd-template-buttons">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadTemplate('elementary')">Elementary Template</button>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadTemplate('secondary')">Secondary Template</button>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadTemplate('university')">University Template</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAllFields()">Clear All</button>
            </div>
        `;
        templateSection.style.cssText = 'margin: 20px 0; padding: 15px; border: 1px dashed #ccc; border-radius: 5px;';

        var planningInterface = document.querySelector('.ubd-planning-interface');
        if (planningInterface) {
            planningInterface.insertBefore(templateSection, planningInterface.firstChild.nextSibling);
        }
    }

    /**
     * Add export button
     */
    function addExportButton() {
        var saveSection = document.querySelector('.ubd-save-section');
        if (saveSection) {
            var exportButton = document.createElement('button');
            exportButton.type = 'button';
            exportButton.className = 'btn btn-outline-success';
            exportButton.innerHTML = '📄 Export UbD Plan';
            exportButton.style.marginLeft = '10px';
            exportButton.addEventListener('click', exportUbDPlan);

            saveSection.appendChild(exportButton);
        }
    }

    /**
     * Load UbD template
     */
    window.loadTemplate = function(templateType) {
        var templates = {
            elementary: {
                enduring: "Students will understand that:\n• Reading is a process of making meaning from text\n• Mathematical concepts connect to real-world situations\n• Scientific inquiry helps us understand our world",
                questions: "• How do good readers make sense of what they read?\n• Why is math important in everyday life?\n• What makes a good scientific question?",
                knowledge: "Students will know:\n• Basic reading strategies\n• Fundamental math operations\n• Scientific method steps\n\nStudents will be able to:\n• Read grade-level texts with comprehension\n• Solve multi-step problems\n• Conduct simple experiments",
                performance: "• Create a reading response journal\n• Solve real-world math problems\n• Design and conduct a science experiment\n• Present findings to classmates",
                evidence: "• Reading comprehension quizzes\n• Math problem-solving rubrics\n• Science lab reports\n• Peer evaluations\n• Self-reflection journals",
                activities: "Week 1-2: Introduction and baseline assessment\nWeek 3-4: Skill building activities\nWeek 5-6: Practice and application\nWeek 7-8: Project work and presentations\nWeek 9-10: Assessment and reflection"
            },
            secondary: {
                enduring: "Students will understand that:\n• Historical events are interconnected and influence present-day issues\n• Scientific theories evolve based on evidence\n• Literature reflects and shapes cultural values\n• Mathematical modeling helps solve complex problems",
                questions: "• How do past events shape our current world?\n• What makes a scientific theory reliable?\n• How does literature influence society?\n• When is mathematical modeling most useful?",
                knowledge: "Students will know:\n• Key historical periods and their significance\n• Major scientific theories and evidence\n• Literary devices and their effects\n• Mathematical modeling techniques\n\nStudents will be able to:\n• Analyze primary and secondary sources\n• Evaluate scientific claims\n• Interpret literary works in context\n• Create and use mathematical models",
                performance: "• Research and present on a historical topic\n• Design an experiment to test a hypothesis\n• Write a literary analysis essay\n• Create a mathematical model for a real-world problem",
                evidence: "• Research project rubrics\n• Lab report evaluations\n• Essay assessments\n• Model accuracy and presentation\n• Peer review feedback",
                activities: "Unit 1: Foundation building (Weeks 1-3)\nUnit 2: Skill development (Weeks 4-6)\nUnit 3: Application projects (Weeks 7-9)\nUnit 4: Synthesis and evaluation (Weeks 10-12)\nFinal presentations and reflection (Weeks 13-14)"
            },
            university: {
                enduring: "Students will understand that:\n• Academic disciplines are interconnected and inform each other\n• Critical thinking requires evidence-based reasoning\n• Professional practice demands ethical decision-making\n• Lifelong learning is essential for career success",
                questions: "• How do different academic perspectives enhance understanding?\n• What constitutes valid evidence in this field?\n• How do ethical considerations impact professional practice?\n• What skills will be most valuable for future success?",
                knowledge: "Students will know:\n• Theoretical frameworks in the discipline\n• Research methodologies and their applications\n• Professional standards and ethical guidelines\n• Current trends and future directions\n\nStudents will be able to:\n• Conduct independent research\n• Analyze complex problems from multiple perspectives\n• Communicate findings to diverse audiences\n• Apply ethical reasoning to professional scenarios",
                performance: "• Independent research project\n• Case study analysis and presentation\n• Professional portfolio development\n• Peer review and collaboration exercises\n• Capstone project or thesis",
                evidence: "• Research proposal and final report\n• Case study presentations and peer evaluations\n• Portfolio assessments\n• Professional competency demonstrations\n• Comprehensive examinations or defenses",
                activities: "Module 1: Theoretical foundations (Weeks 1-4)\nModule 2: Research methods and practice (Weeks 5-8)\nModule 3: Applied projects and case studies (Weeks 9-12)\nModule 4: Professional development (Weeks 13-15)\nFinal presentations and portfolio review (Week 16)"
            }
        };

        var template = templates[templateType];
        if (template) {
            if (confirm('This will replace all current content. Are you sure?')) {
                document.getElementById('ubd_enduring').value = template.enduring;
                document.getElementById('ubd_questions').value = template.questions;
                document.getElementById('ubd_knowledge').value = template.knowledge;
                document.getElementById('ubd_performance').value = template.performance;
                document.getElementById('ubd_evidence').value = template.evidence;
                document.getElementById('ubd_activities').value = template.activities;

                // Mark all fields as changed
                var textareas = document.querySelectorAll('.ubd-field textarea');
                textareas.forEach(function(textarea) {
                    textarea.classList.add('ubd-changed');
                    addUnsavedIndicator(textarea);
                });

                hasUnsavedChanges = true;
                updateSaveButtonState();
                showNotification('Template loaded successfully! Remember to save your changes.', 'success');
            }
        }
    };

    /**
     * Clear all fields
     */
    window.clearAllFields = function() {
        if (confirm('This will clear all content. Are you sure?')) {
            var textareas = document.querySelectorAll('.ubd-field textarea');
            textareas.forEach(function(textarea) {
                textarea.value = '';
                textarea.classList.add('ubd-changed');
                addUnsavedIndicator(textarea);
            });

            hasUnsavedChanges = true;
            updateSaveButtonState();
            showNotification('All fields cleared. Remember to save your changes.', 'info');
        }
    };

    /**
     * Export UbD plan
     */
    function exportUbDPlan() {
        var ubdData = {
            stage1_enduring: document.getElementById('ubd_enduring')?.value || '',
            stage1_questions: document.getElementById('ubd_questions')?.value || '',
            stage1_knowledge: document.getElementById('ubd_knowledge')?.value || '',
            stage2_performance: document.getElementById('ubd_performance')?.value || '',
            stage2_evidence: document.getElementById('ubd_evidence')?.value || '',
            stage3_activities: document.getElementById('ubd_activities')?.value || ''
        };

        var exportContent = generateExportContent(ubdData);
        downloadFile('UbD_Plan_' + new Date().toISOString().split('T')[0] + '.txt', exportContent);
        showNotification('UbD plan exported successfully!', 'success');
    }

    /**
     * Generate export content
     */
    function generateExportContent(data) {
        return `UbD (Understanding by Design) Course Plan
Generated on: ${new Date().toLocaleDateString()}

========================================
STAGE 1: DESIRED RESULTS (预期学习成果)
========================================

ENDURING UNDERSTANDINGS (持久理解):
${data.stage1_enduring}

ESSENTIAL QUESTIONS (核心问题):
${data.stage1_questions}

KNOWLEDGE & SKILLS (知识与技能):
${data.stage1_knowledge}

========================================
STAGE 2: ASSESSMENT EVIDENCE (评估证据)
========================================

PERFORMANCE TASKS (表现性任务):
${data.stage2_performance}

OTHER EVIDENCE (其他证据):
${data.stage2_evidence}

========================================
STAGE 3: LEARNING PLAN (教学活动)
========================================

LEARNING ACTIVITIES (学习活动):
${data.stage3_activities}

========================================
End of UbD Plan
========================================`;
    }

    /**
     * Download file
     */
    function downloadFile(filename, content) {
        var element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(content));
        element.setAttribute('download', filename);
        element.style.display = 'none';
        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);
    }

    /**
     * Save UbD plan via AJAX
     */
    function saveUbDPlan(isAutoSave) {
        return new Promise(function(resolve, reject) {
            console.log('Saving UbD plan...');

            // Get course ID from the page
            var courseId = M.cfg.courseId || document.querySelector('[data-courseid]')?.getAttribute('data-courseid');
            if (!courseId) {
                console.error('Course ID not found');
                reject(new Error('Course ID not found'));
                return;
            }

            // Show saving indicator for manual saves
            if (!isAutoSave) {
                var saveButton = document.getElementById('save-ubd-plan');
                if (saveButton) {
                    saveButton.disabled = true;
                    saveButton.innerHTML = '💾 Saving...';
                }
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
                // Reset save button for manual saves
                if (!isAutoSave) {
                    var saveButton = document.getElementById('save-ubd-plan');
                    if (saveButton) {
                        saveButton.disabled = false;
                        saveButton.innerHTML = 'Save UbD Plan';
                    }
                }

                if (data.success) {
                    console.log('UbD plan saved successfully');

                    if (!isAutoSave) {
                        // Show success message for manual saves
                        showNotification('UbD plan saved successfully!', 'success');

                        // Clear all unsaved indicators
                        var textareas = document.querySelectorAll('.ubd-field textarea');
                        textareas.forEach(function(textarea) {
                            textarea.classList.remove('ubd-changed');
                            removeUnsavedIndicator(textarea);
                        });

                        hasUnsavedChanges = false;
                        updateSaveButtonState();
                    }

                    resolve(data);
                } else {
                    console.error('Error saving UbD plan:', data.message);
                    if (!isAutoSave) {
                        showNotification('Error saving UbD plan: ' + data.message, 'error');
                    }
                    reject(new Error(data.message));
                }
            })
            .catch(error => {
                console.error('AJAX error:', error);

                // Reset save button for manual saves
                if (!isAutoSave) {
                    var saveButton = document.getElementById('save-ubd-plan');
                    if (saveButton) {
                        saveButton.disabled = false;
                        saveButton.innerHTML = 'Save UbD Plan';
                    }
                    showNotification('Network error occurred while saving', 'error');
                }

                reject(error);
            });
        });
    }

    /**
     * Show notification message
     */
    function showNotification(message, type) {
        // Remove existing notifications
        var existingNotifications = document.querySelectorAll('.ubd-notification');
        existingNotifications.forEach(function(notification) {
            notification.remove();
        });

        // Create notification element
        var notification = document.createElement('div');
        notification.className = 'ubd-notification ubd-notification-' + type;
        notification.innerHTML = message;

        var backgroundColor;
        switch(type) {
            case 'success':
                backgroundColor = '#28a745';
                break;
            case 'error':
                backgroundColor = '#dc3545';
                break;
            case 'warning':
                backgroundColor = '#ffc107';
                break;
            case 'info':
                backgroundColor = '#17a2b8';
                break;
            default:
                backgroundColor = '#6c757d';
        }

        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            z-index: 10001;
            background-color: ${backgroundColor};
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: slideInRight 0.3s ease-out;
            max-width: 300px;
            word-wrap: break-word;
        `;

        // Add close button
        var closeButton = document.createElement('span');
        closeButton.innerHTML = '×';
        closeButton.style.cssText = `
            float: right;
            margin-left: 10px;
            cursor: pointer;
            font-size: 1.2em;
            line-height: 1;
        `;
        closeButton.addEventListener('click', function() {
            notification.remove();
        });
        notification.appendChild(closeButton);

        document.body.appendChild(notification);

        // Add CSS animation if not already added
        if (!document.getElementById('ubd-notification-styles')) {
            var style = document.createElement('style');
            style.id = 'ubd-notification-styles';
            style.textContent = `
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes slideOutRight {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        // Remove after 5 seconds with animation
        setTimeout(function() {
            if (notification.parentNode) {
                notification.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(function() {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }
        }, 5000);
    }

    // Make saveUbDPlan available globally for the save button
    window.saveUbDPlan = function() {
        saveUbDPlan(false);
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUbDFormat);
    } else {
        initUbDFormat();
    }

})();
