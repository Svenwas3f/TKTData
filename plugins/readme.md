# Plugin Guide

###### Contents
1. Requirements
2. Basics
3. Advanced infos



### Requirements
A Plugin is stored in the current folder (`/plugins/ . . .`). The folder name is as well the plugin name. Please try to avoid empty spaces, special chars and others. This may occur problems.

The folder contains at least 3 documents. Firstly there is stylesheet file called `style.css`. This file can be used to add custom css.

Secondly there is a file called `functions.php`. This file contains all informations that are used to modifies the whole system. ex. if you want to add a new page to the system etc.

Thirdly there is a file called `index.php`. This document contains the content of the page you added. You can use pure html in this file or combine it with js, css and php.

Feel free to create own files, but be aware that you need to include those files. It is recommended to include these files in `functions.php` and with the following code `require_once( dirname(__FILE__) . "/your_file.your_extension")`

### Basics
###### First steps
To create basics you first need to start a class. Do this the following way
> $plugin = new Plugin();

###### Add a page
If you want to add a page to the system, navigate to the `functions.php` file. To create a new page you need to add the following code. The page is a title for subpages and is not a "real page". You can replace all parameters custom. Do not go lower than 6 by the layout because pages 1 to 5 are blocked ones who are pre-installed and cannot be modified.
> $mainpage = $plugin->add_page(array(\
>     "name" => "Testpage",\
>     "layout" => 6\
> ));

To add an actual page as we know use the following code. Replace the parameters as you want. Important is to set the mainpage to the return of the function add_page what returns the id or false of the page. You can start your layout wherever you want because this is in the container and you can add as many subpages as you want. If you want to add an image place the image in the plugin folder and give the absolute path in the plugin folder ex. `medias/page.png`. Do not write the plugin name before the path.

>$plugin->add_subpage(array(\
>  "name" => "sub",\
>  "mainpage" => $mainpage,\
>  "image" => null,\
>  "layout" => 1,\
>));

###### Index file
In the index file we have all our content. You can get the current page by the following variable `$page`. If you want to get the name of the page use the following code

> $plugin = new Plugin();\
> $name = $plugin->get_page( $page );

It is recommended to use a switch statement in the `index.php` file.

>switch( $name ) {\
>   case "your_page": \
>     echo "Hello World";\
>   break;\
> }


### Advanced Infos

You can use any function that is listed in `/php/*`. All functions can be used.

To access the database use the following code
> $conn = Access::connect();
