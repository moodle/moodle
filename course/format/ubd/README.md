# UbD Course Format Plugin for Moodle

## 🎯 Overview

The **UbD (Understanding by Design) Course Format** plugin brings the powerful Understanding by Design framework to Moodle, enabling educators to design courses that focus on deep understanding and meaningful learning.

This plugin is part of the **Moodle Evolved** project, aimed at enhancing Moodle with structured pedagogical frameworks.

## ✨ Features

### 🏗️ Three-Stage UbD Framework

#### **Stage 1: Desired Results (预期学习成果)**
- **Enduring Understandings**: What should students retain long after the course?
- **Essential Questions**: What provocative questions will foster inquiry?
- **Knowledge & Skills**: What should students know and be able to do?

#### **Stage 2: Assessment Evidence (评估证据)**
- **Performance Tasks**: What authentic tasks will reveal evidence of understanding?
- **Other Evidence**: What other evidence will confirm understanding?

#### **Stage 3: Learning Plan (教学活动)**
- **Learning Activities**: What learning experiences will enable students to achieve desired results?

### 🚀 Advanced Features

- **📱 Responsive Design**: Works seamlessly on desktop, tablet, and mobile devices
- **💾 Auto-save**: Automatically saves your work every 30 seconds
- **📋 Templates**: Pre-built templates for Elementary, Secondary, and University levels
- **📄 Export**: Export your UbD plans as text files
- **🎨 Customizable**: Color-coded stages with admin customization options
- **🔄 Real-time Validation**: Instant feedback on content length and structure
- **🌐 Bilingual**: English and Chinese language support

## 📦 Installation

### Method 1: Manual Installation
1. Download the plugin files
2. Extract to `[moodle-root]/course/format/ubd/`
3. Visit Site Administration → Notifications to complete installation

### Method 2: Git Installation
```bash
cd [moodle-root]/course/format/
git clone [repository-url] ubd
```

### Method 3: Moodle Plugin Directory
1. Go to Site Administration → Plugins → Install plugins
2. Search for "UbD Course Format"
3. Click Install

## 🛠️ Configuration

### Admin Settings
Navigate to **Site Administration → Plugins → Course formats → UbD Format**

- **Auto-save interval**: Configure how often to auto-save (15s to 5min, or disable)
- **Content limits**: Set maximum field and total content lengths
- **Templates**: Enable/disable template functionality
- **Export**: Enable/disable export functionality
- **Stage colors**: Customize the color scheme for each stage
- **Default visibility**: Choose which stages are expanded by default

### Course Settings
When creating a new course:
1. Set **Course format** to "UbD Format (Understanding by Design)"
2. Configure standard course settings as needed
3. Save and continue to access the UbD planning interface

## 📚 Usage Guide

### Getting Started
1. Create a new course with UbD format
2. Navigate to the course page
3. Use the structured interface to plan your course following UbD principles

### Using Templates
1. Click on a template button (Elementary, Secondary, or University)
2. Review the pre-filled content
3. Customize to match your specific course needs
4. Save your changes

### Auto-save and Manual Save
- **Auto-save**: Runs automatically every 30 seconds (configurable)
- **Manual save**: Click "Save UbD Plan" button anytime
- **Visual indicators**: Unsaved changes are marked with colored dots

### Exporting Your Plan
1. Complete your UbD planning
2. Click "Export UbD Plan"
3. A text file will be downloaded with your complete plan

## 🎨 User Interface

### Color-Coded Stages
- **Stage 1**: Pink/Magenta - Focuses on desired outcomes
- **Stage 2**: Orange - Emphasizes assessment and evidence
- **Stage 3**: Green - Details learning activities and instruction

### Interactive Elements
- **Collapsible stages**: Click stage headers to expand/collapse
- **Character counters**: Real-time feedback on content length
- **Progress indicators**: Visual feedback during saving
- **Responsive notifications**: Success, warning, and error messages

## 🧪 Testing

### Running Unit Tests
```bash
# Run all UbD format tests
php admin/tool/phpunit/cli/util.php --buildconfig
php vendor/bin/phpunit course/format/ubd/tests/

# Run specific test
php vendor/bin/phpunit course/format/ubd/tests/format_ubd_test.php
```

### Manual Testing Checklist
- [ ] Create course with UbD format
- [ ] Enter content in all six fields
- [ ] Test auto-save functionality
- [ ] Load different templates
- [ ] Export UbD plan
- [ ] Test on mobile devices
- [ ] Verify data persistence

## 🔧 Development

### File Structure
```
course/format/ubd/
├── version.php              # Plugin metadata
├── lib.php                  # Main format class
├── format.php               # Display logic
├── renderer.php             # Custom renderer
├── ajax.php                 # AJAX handlers
├── format.js                # JavaScript functionality
├── settings.php             # Admin settings
├── lang/en/format_ubd.php   # Language strings
├── db/upgrade.php           # Database upgrades
├── tests/format_ubd_test.php # Unit tests
└── README.md                # This file
```

### Key Classes
- `format_ubd`: Main course format class
- `format_ubd_renderer`: Custom renderer for UbD-specific output

### JavaScript API
- `saveUbDPlan()`: Manual save function
- `loadTemplate(type)`: Load predefined templates
- `clearAllFields()`: Clear all content
- `exportUbDPlan()`: Export functionality

## 🤝 Contributing

We welcome contributions! Please:

1. Fork the repository
2. Create a feature branch
3. Follow Moodle coding standards
4. Add unit tests for new functionality
5. Submit a pull request

### Coding Standards
- Follow [Moodle Coding Style](https://docs.moodle.org/dev/Coding_style)
- Use meaningful variable and function names
- Add PHPDoc comments for all functions
- Include language strings for all user-facing text

## 📄 License

This plugin is licensed under the GNU General Public License v3.0 or later.
See [LICENSE](LICENSE) for full license text.

## 🆘 Support

- **Documentation**: [Moodle Docs](https://docs.moodle.org/)
- **Issues**: Report bugs and feature requests via GitHub Issues
- **Community**: Join the Moodle community forums

## 🙏 Acknowledgments

- **Grant Wiggins & Jay McTighe**: Creators of the Understanding by Design framework
- **Moodle Community**: For the excellent platform and development resources
- **Contributors**: All developers who have contributed to this project

---

**Part of the Moodle Evolved project** - Enhancing education through better course design! 🚀
