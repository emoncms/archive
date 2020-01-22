# Energy Audit module

based on David MacKay's work Sustainable Energy without the hot air
withouthotair.com

Module Author: Trystan Lea

To install:

1) Download and place in Emoncms Modules folder 

2) Run database setup: uncomment //db_schema_setup(load_db_schema()); in index.php and refresh the page once, recomment again after running once.

3) Add a menu item to settings.php for quick access, such as:

  $menu_left[] = array('name'=>"My Energy", 'path'=>"energyaudit/electric" , 'session'=>"write");
