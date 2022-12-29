<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div id="app">
    <h3>/api/history/{alcohol} searcher</h3>
    <p>
        forgive this monstrosity, for it is but a fun toy
        <br>
        this does NOT support fuzzy search -- <b><i>you have to input a correct title or a substring of a title.</i></b>
        <br>
    </p>
    <search-page></search-page>
    @vite('resources/js/app.js')
</div>
</body>
</html>
