<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('/web/css/general.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/home.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/head.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/vars.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/buttons.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/profile.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/catalog.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/categories.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/subcategories.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/product.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/subcatsPanel.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/productManager.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/designManager.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/eventManager.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/eventsPanel.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/order.css')}}">
    <link rel="stylesheet" href="{{asset('/web/css/event.css')}}">
    <script src="https://unpkg.com/vue@3"></script><!--/dist/vue.min.js - append to path in release-->
    <title>HoneVeg</title>
  </head>
  
  <body style="z-index: 1;">
    <div>
      {{$slot}}
    </div>
  </body>
  
</html>