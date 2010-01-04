/**
 * Manual allocator
 */

.manual-allocator .allocations {
    margin: 0px auto;
}

.manual-allocator .allocations .r0 {
    background-color: #eee;
}

.manual-allocator .allocations .highlightreviewedby .reviewedby,
.manual-allocator .allocations .highlightreviewerof .reviewerof {
    background-color: #fff3d2;
}

.manual-allocator .allocations tr td {
    vertical-align: top;
    padding: 5px;
}

.manual-allocator .allocations tr td.peer {
    border-left: 1px solid #ccc;
    border-right: 1px solid #ccc;
}

.manual-allocator .allocations .reviewedby .info,
.manual-allocator .allocations .peer .info,
.manual-allocator .allocations .reviewerof .info {
    font-size: 80%;
    color: #888;
    font-style: italic;
}

.manual-allocator .allocations .reviewedby img.userpicture,
.manual-allocator .allocations .reviewerof img.userpicture {
    height: 16px;
    width: 16px;
    margin-right: 3px;
    vertical-align: middle;
}

.manual-allocator .allocations .peer img.userpicture {
    height: 35px;
    width: 35px;
    vertical-align: middle;
    margin-right: 5px;
}

.manual-allocator .allocations .peer .submission {
    font-size: 90%;
    margin-top: 1em;
}

.manual-allocator .status-message {
    padding: 5px 5em 5px 15px;
    margin: 0px auto 20px auto;
    width: 60%;
    font-size: 80%;
    position: relative;
}

.manual-allocator .status-message-closer {
    font-weight: bold;
    position: absolute;
    top: 5px;
    right: 15px;
}

.manual-allocator .status-message.ok {
    color: #547c22;
    background-color: #e7f1c3;
}

.manual-allocator .status-message.error {
    color: #dd0221;
    background-color: #ffd3d9;
}

.manual-allocator .status-message.info {
    color: #1666a9;
    background-color: #d2ebff;
}


