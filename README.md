# RockIframe

## A message to Russian 🇷🇺 people

If you currently live in Russia, please read [this message](https://github.com/Roave/SecurityAdvisories/blob/latest/ToRussianPeople.md).

[![SWUbanner](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

---

Iframe Sidebar for the ProcessWire page edit screen

## Problem

Sometimes the editor wants to see a preview of a file while editing a page. For example this can be helpful when digitizing invoices. Regular ProcessWire panels can be used for preview but overlap the page editor so the user has to open and close the panel repeatedly while working.

RockIframe shows a preview of any content that can be rendered in an iframe and keeps the page editor 100% visible to the user.

## Usage

Simply call `$modules->get('RockIframe')->show('path/to/your/file.pdf')` anywhere in the admin to show your data in a sidebar iframe on page load. You can also define a Pagefile(s) object as source of the iframe.

## Example

This example shows the first file of the field `myfilesfield` on the page edit screen of pages having template `mypagetemplate`:

```php
$wire->addHookAfter("ProcessPageEdit::buildForm", function($event) {
  $page = $event->process->getPage();
  if($page->template !== 'mypagetemplate') return;

  /** @var RockIframe $iframe */
  $iframe = $this->wire->modules->get('RockIframe');
  if($iframe) $iframe->show($page->get('myfilesfield'));
});
```

![img](https://i.imgur.com/9e5KvTY.png)

```php
$wire->addHookAfter("ProcessPageEdit::buildForm", function($event) {
  $page = $event->process->getPage();
  if($page->template !== 'mypagetemplate') return;

  /** @var RockIframe $iframe */
  $iframe = $this->wire->modules->get('RockIframe');
  if($iframe) $iframe->show("http://www.example.com");
});
```

![img](https://i.imgur.com/a8aEQIs.png)

## Ideas & Notes

PDF preview relies on the browser's capability to preview PDFs. Also at the moment there are no checks which content is thrown to the iframe as source. For images for example we could use https://leafletjs.com/examples/crs-simple/crs-simple.html to enable panning & zooming on the image. For better browsersupport of PDF we could use https://mozilla.github.io/pdf.js/web/viewer.html;

Mobile view is not yet taken care of... Ideas or help welcome - but I'm using it for desktop-only and backend-only apps at the moment.
