# ModUI-php
Modular User Interface

#Requirements
* [ryo33/lwte](https://github.com/ryo33/lwte.js)  
  
#Usage
* Create a instance of any components which inherited `ModUIContainer`.  
`$nc = new NormalContainer();`
* Create a instance of `ModUI`.  
`$mod = new ModUI($nc);`
* Add your containers and components to `$mod` or `$nc` or **your countainers**.  
`$mod->add(new YOUR_COMPONENT());`
* Write if-statement which detects request method `GET` or `POST`.  
```php
if ($_SERVER['REQUEST_METHOD'] === "GET") {
  // GET
} else if ($_SERVER['REQUEST_METHOD'] === "POST") {
  // POST
}
```
* Call `$mod->display` and echo results when request method is `GET`.  
```php
  // GET
  // Print header of your page.
  echo '<script>';
  $result = $mod->display('example', <<<EOJS
    function(name, value, update){
      // If you use jQuery
      $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
        data: { "name": name, "value": JSON.stringify(value)}
      }).done(function(data){
        update(data);
      });
    }
EOJS
  , <<<EOJS
    function(update){
      // If you use jQuery
      $.ajax({
        url: window.location.pathname,
        method: "POST",
        dataType: "json",
      }).done(function(data){
        update(data);
      });
    }
EOJS
  );
  foreach ($result['templates'] as $name => $template) {
    // Remove newline characters from $template, if your templates include them.
    echo 'lwte.addTemplate("$name", "$template");
  }
  // If you use jQuery
  echo '$("body").text(lwte.useTemplate("example", "' . json_encode($result['values']) . '"))';
  echo $result['script'];
  echo '</script>';
  // Print footer of your page.
```
* Call `$mod->input` when request method is `POST`.  
```php
  // POST
  $mod->input($_POST);
```
* That's all. Your page works.
