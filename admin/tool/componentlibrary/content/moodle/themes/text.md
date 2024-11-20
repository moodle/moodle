---
layout: docs
title: "Text"
description: "Moodle text utility classes"
date: 2019-12-10T13:53:41+01:00
draft: false
---

## Heading sizes with native heading tags
{{< example >}}
<h1>h1 heading</h1>
<h2>h2 heading</h2>
<h3>h3 heading</h3>
<h4>h4 heading</h4>
<h5>h5 heading</h5>
<h6>h6 heading</h6>
{{< /example >}}

## Heading sizes with heading classes

tip: you can use heading classes to style a native heading tag differently.

{{< example >}}
<p class="h1">h1 heading</p>
<p class="h2">h2 heading</p>
<h1 class="h3">h3 heading</h1>
<p class="h4">h4 heading</p>
<p class="h5">h5 heading</p>
<p class="h6">h6 heading</p>
{{< /example >}}

## Native text tags

{{< example >}}
<p>You can use the mark tag to <mark>highlight</mark> text.</p>
<p><del>This line of text is meant to be treated as deleted text.</del></p>
<p><s>This line of text is meant to be treated as no longer accurate.</s></p>
<p><ins>This line of text is meant to be treated as an addition to the document.</ins></p>
<p><u>This line of text will render as underlined</u></p>
<p><small>This line of text is meant to be treated as fine print.</small></p>
<p><strong>This line rendered as bold text.</strong></p>
<p><em>This line rendered as italicized text.</em></p>
{{< /example >}}

## Custom text tags

{{< example >}}
<p class="text-lowercase">Lowercased text.</p>
<p class="text-uppercase">Uppercased text.</p>
<p class="font-weight-bold">Bold text.</p>
<p class="font-weight-normal">Normal weight text.</p>
<p class="font-italic">Italic text.</p>

<p class="text-muted">
    Muted text with a <a href="#" class="text-reset">reset link</a>.
</p>
{{< /example >}}

## For screenreaders

<h2 class="sr-only">Title for screen readers</h2>
<a class="sr-only-focusable" href="#content">Skip to main content</a>

## Text truncation

<!-- Block level -->
<div class="row">
  <div class="col-2 text-truncate">
    Praeterea iter est quasdam res quas ex communi.
  </div>
</div>

<!-- Inline level -->
<span class="d-inline-block text-truncate" style="max-width: 150px;">
  Praeterea iter est quasdam res quas ex communi.
</span>
