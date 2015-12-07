# Node module (alpha)

Simple markdown page creator module. Very much in alpha at the moment. 

Module Author: Trystan Lea

To install:

1) Download and place in Emoncms Modules folder 

2) Run database setup: uncomment //db_schema_setup(load_db_schema()); in index.php and refresh the page once, recomment again after running once.

3) Add a menu item to settings.php for quick access, such as:

  $menu_left[] = array('name'=>"Node", 'path'=>"node/list" , 'session'=>"write");
