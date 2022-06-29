---
layout: docs
title: "Grids"
descriptions: Using grids in Moodle
date: 2020-02-04T09:40:32+01:00
draft: false
weight: 1
---

Use the bootstrap grid column classes to create responsive grids. Rules to follow:

* Always wrap rows in a container
* Combine column classes to create responsive grids
* Keep the context in mind, modals behave different from #region-main
* Don't add to much styles to the grid container, us an inner div


{{< example>}}
<div class="container-fluid">
  <div class="row">
    <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3">
      <div class="inner h-100 border p-1">
      Lorem ipsum dolor sit amet
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3">
      <div class="inner h-100 border p-1">
      Lonsectetuer adipiscing elit. Aenean commodo ligula eget dolor.
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3">
      <div class="inner h-100 border p-1">
      Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3">
      <div class="inner h-100 border p-1">
    ascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu.
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3">
      <div class="inner h-100 border p-1">
    Lretium quis, sem. Nulla consequat massa quis enim.
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3">
      <div class="inner h-100 border p-1">
    Aenean commodo massa quis enim.
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3">
      <div class="inner h-100 border p-1">
    Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3">
      <div class="inner h-100 border p-1">
    Aenean commodo ligula eget dolor. Aenean massa. Cu quis enim.
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3">
      <div class="inner h-100 border p-1">
    Loltricies nec, pellentesque eu, quis enim.
      </div>
    </div>
    <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-3">
      <div class="inner h-100 border p-1">
    Ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis.
      </div>
    </div>
  </div>
</div>
{{< /example >}}

If needed
