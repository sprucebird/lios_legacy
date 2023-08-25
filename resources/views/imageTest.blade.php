<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    @foreach ($img as $image)
      <img src={{$image->getUrl('jpg')}}>
    @endforeach
  </body>
</html>
