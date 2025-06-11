---
layout: docs
title: "Positioning"
description: The use of Bootstraps flexbox utilities to position items on the screen.
date: 2020-02-04T09:40:32+01:00
draft: false
weight: 3
---
##
## Position an single item at the right

Use the ```.justify-content-end``` class to position on item in a ```.d-flex``` container to the right

{{< example >}}
<div class="d-flex justify-content-end">
  <button class="btn btn-success">OK</button>
</div>
{{< /example >}}


## Position on item in a group of items to the right

Use the ```.ms-auto``` to move the last item in ad ```.d-flex``` container to the right.

{{< example >}}
<div class="d-flex">
  <button class="btn btn-secondary me-1">Ha!</button>
  <button class="btn btn-secondary me-1">Jay</button>
  <button class="btn btn-secondary me-1">Wow</button>
  <button class="ms-auto btn btn-success">OK</button>
</div>
{{< /example >}}

### Center items

Use the ```align-items-center``` class to align items horizontally in a container.

{{< example >}}
<div class="d-flex align-items-center p-2 bg-light">
    <div class="bg-success me-2" style="width: 35px; height: 35px;"></div>
    <div class="bg-warning me-2" style="width: 48px; height: 48px;"></div>
    <div class="bg-info me-2" style="width: 20px; height: 20px;"></div>
</div>
{{< /example >}}

### Middle of the container

Combine the ```align-items-center``` with the ```justify-content-center``` class to position an element in the middle of a container.

{{< example >}}
<div class="d-flex align-items-center justify-content-center p-3 bg-light" style="height:100px;">
    <div class="bg-warning me-2" style="width: 48px; height: 48px;"></div>
</div>
{{< /example >}}

