This is the repository for group-8-lovely-things
group project for CE0902A.

Let's try and keep to a certain coding standard 
so that all our snippets and code will look 
similar and not out of place. Have a look below.

I'm suggesting to also use a tab size of 2, 
replaced by spaces. This is simply habit. Let me 
know if you have a strong preference for a 
different set-up.

I've also got a preference for strings being 
encapsulated in single quotes in CSS, JS and PHP.
Again, if anyone has got a stronger preference 
for using double quotes in all cases, then please
let me know.

----------
  HTML
----------

<!DOCTYPE html>
<html>
<head>
  <title></title>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.0/jquery-ui.min.js"></script>
</head>
<body>

<h1>Heading 1</h1>
<h2>Heading 2</h2>
<p>Paragraph text...</p>
<!-- This is an HTML comment -->
<!-- Remember - only use tables for tabular data! -->

</body>
</html>

----------
==========
----------
   CSS
----------

/*
  Big, important comment/message at the top of the CSS
  file, if applicable and needed.
*/

body {
  width: 960px;
  margin: 0 auto;
  background-color: #EEE;
  font: 16px arial, sans-serif;
  /* Note the spacings used */
}

/* CSS style comments */
h1, h2, h3 {
  font-family: 'Georgia', serif;
}

#object_id .object_class p img {
  float: left;
}

----------
==========
----------
JavaScript
----------

/*
  Make use of jQuery code, we've got it at our disposal
  and it will make our lives much easier!
*/
$(function() {
  alert('Again, keep the same styling throughout the application.');
});

/**
 * Commenting code and at least functions will help us a lot
 * of struggle later down the line when 3 different people
 * have committed changes and might have forgotten what the 
 * code was meant to do.
 * 
 * This is conventionally a function comment.
 */
function testFunc() {
  var opacityEnd = 0;
  // Break lines like this if you need to:
  non_existent_function(
    $('#target').css({position: relative;}).animate({left: '+-20', opacity: opacityEnd})
  );
  // That line above is probably syntactically invalid...
}

----------
==========
----------
   PHP
----------

/*
  This is quite similar to JavaScript in terms of style of coding.
*/

/**
 * Like in Javascript, this DocBlock denotes a function
 * comment or function description.
 */
function genRandomString($length = 8, $characters = '0123456789abcdefghijklmnopqrstuvwxyz') {
  $string = '';
  for ($i = 0; $i < $length; $i++) {
    $string .= $characters[mt_rand(0, strlen($characters) - 1)];
  }
  return $string;
}

// The following two echo lines work exactly the same, noticing the commas and dots.
/*
  If you are echoing HTML code, I would strongly suggest to always
  insert PHP_EOL (end-of-line character) in order to preserve the
  consistency of the generated code. This can be helpful if you are
  trying to troubleshoot an HTML error and you are using the
  generated source to view your results.
*/
echo 'Random code generated: ', genRandomString(10), PHP_EOL;
echo 'Random code generated: ' . genRandomString(10) . PHP_EOL;

----------
