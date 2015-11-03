# ModUI-php
Modular User Interface

#Requirements
* [ryo33/lwte](https://github.com/ryo33/lwte.js)  
  
#Usage
* Create a instance of any components which inherited `ModUIContainer`.  
`$nc = new NormalContainer();`
* Create a instance of `ModUI`.  
`$mod = new ModUI("TEMPLATE_NAME", $nc);`
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
  $result = $mod->display(<<<EOJS
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
  , 5000); // Your page will be updated every 5 seconds.
  foreach ($result['templates'] as $name => $template) {
    // Remove newline characters from $template, if your templates include them.
    echo 'lwte.addTemplate("$name", "$template");
  }
  // If you use jQuery
  echo '$("body").text(lwte.useTemplate("TEMPLATE_NAME", "' . json_encode($result['values']) . '"))';
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

#How to make your Component
* Components should extends ModUIComponent.  
* You can implement the following functions to make Component.  
    - get_template_name(name: string) -> template_name: string
        * You don't have to implement this function.  
        * But you should implement this to returns the unique string (e.g. `return 'UNIQUE_STRING';`) if function get_templates always returns the same result and your component will be used many times in one page.  
    - get_templates(name: string) -> templates: [string => string,]
        * You must implemnt this if your component has a appearance.  
        * See [lwte](https://github.com/ryo33/lwte.js) to learn how to write template
    - get_values(name: string) -> values: [string => any,]
        * This function must return values used by templates.  
    - get_scripts(name: string) -> scripts: [string => string,]
        * You must implement this when your component accepts user input.  
        * This function should returns a map of javascript code.
    - input(name: string, value: string) -> None
        * You must implement this when your component accepts user input.  
        * This function will be called when your component gets an input.  

#Example Component
* textbox and button 
```php
class TextboxAndButton extends ModUIComponent {
  private $con;
  public function __construct($con) {
    $this->con = $con;
  }
  public function get_template_name($name) {
    return $name;
  }
  public function get_templates($name) {
    // _name is unique name
    $template = <<<TMPL
      <input id="{_name}" type="text" value="{value}">
      <button id="{_name}-button">update</button>
TMPL;
    return [$this->get_template_name($name) => $template]; // You can returns more templates if you need
  }
  public function get_values($name) {
    return ['value' => $this->_con->get('key')]; // You can returns more values if you need
  }
  public function get_scripts($name) {
    return [
      'value' => 'function(selector){return $("#" + selector).val();}', // javascript function which returns a value of component
      'event' => 'function(selector, update){$(document).on("click", "#" + selector + "-button", update);}' // javascript function which registers events
    ];
  }
  public function input($name, $value) {
    // $name should be NULL.
    $this->con->set('key', $value); // access to database
  }
}
```
