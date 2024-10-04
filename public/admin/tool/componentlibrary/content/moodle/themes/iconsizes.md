---
layout: docs
title: "Icon Sizes"
date: 2020-04-27T09:40:32+01:00
draft: false
weight: 5
---

Moodle icons are usually rendered using the template ```{{pix}}``` helper.

For example:

```{{#pix}}t/up, moodle, {{#str}} up, moodle {{/str}}{{/pix}}```

which results in

{{< example show_preview=true >}}
  <i class="icon fa fa-arrow-up fa-fw " title="Up" aria-label="Up"></i>
{{< /example >}}

## Controlling the icon size

Use the ```icon-size-x``` classes to control the icon sizes.

{{< example show_markup=false >}}
<div class="d-flex">
  <div class="icon-size-1 card bg-light me-2">
    <div class="card-body d-flex justify-content-center align-items-center">
    <i class="fa fa-arrow-up icon"></i>
    </div>
    <div class="card-footer">
    icon-size-1<br>4px
      </div>
  </div>
  <div class="icon-size-2 card bg-light me-2">
    <div class="card-body d-flex justify-content-center align-items-center">
    <i class="fa fa-arrow-up icon"></i>
    </div>
    <div class="card-footer">
    icon-size-2<br>8px
      </div>
  </div>
  <div class="icon-size-3 card bg-light me-2">
    <div class="card-body d-flex justify-content-center align-items-center">
    <i class="fa fa-arrow-up icon"></i>
    </div>
    <div class="card-footer">
    icon-size-3<br>16px (default)
      </div>
  </div>
  <div class="icon-size-4 card bg-light me-2">
    <div class="card-body d-flex justify-content-center align-items-center">
    <i class="fa fa-arrow-up icon"></i>
    </div>
    <div class="card-footer">
    icon-size-4<br>24px
      </div>
  </div>
  <div class="icon-size-5 card bg-light me-2">
    <div class="card-body d-flex justify-content-center align-items-center">
    <i class="fa fa-arrow-up icon"></i>
    </div>
    <div class="card-footer">
    icon-size-5<br>32px
      </div>
  </div>
  <div class="icon-size-6 card bg-light me-2">
    <div class="card-body d-flex justify-content-center align-items-center">
    <i class="fa fa-arrow-up icon"></i>
    </div>
    <div class="card-footer">
    icon-size-6<br>40px
      </div>
  </div>
</div>
{{< /example >}}
