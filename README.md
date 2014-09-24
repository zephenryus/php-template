php-template
============

A PHP template class based on AngularJS

Currently it is not fully fleshed out, but it does feature simple binding replacement

# Example

```php
$data = array(
  "loginScreenHeadingHref" => "http://www.example.com/",
  "login-header" => "Sample Login"
);

$template = new Template (
  ".\\templates\\login-form.html",
  $data
);

print $template->getOutput();

```

Where `login-form.html` has the following content:

```html
<div id="login-wrapper">
  <h1><a href="{{ loginScreenHeadingHref }}">{{login-header}}</a></h1>
</div>

```

would yeild

```html
<div id="login-wrapper">
  <h1 id="logo"><a href="http://www.example.com/">Sample Login</a></h1>
</div>
```
