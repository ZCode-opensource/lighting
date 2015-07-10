v 0.0.2

v 0.0.1
    - Added version again
    - Added main log
    - Separation between framework and web site/application code
    - Added base factory and main factory for constructing objects
    - Removed debug info from constructor in base object
    - Fixed some bugs on the Http classes
    - Modified Application class to use new factory methods
    - Fixed configuration class and added session class
    - Removed index.php that was added by mistake
    - Fix for the session class with incorrect namespace
    - Bare module loading implemented
    - Implemented base classes for controller and view, also added template class
    - Implemented render of main template
    - Implemented module views
    - Added mysql database functionality
    - Added load of css and js files inside modules
    - Fixed problem with ajax calls
    - Added json response method to base controller
    - Implemented global auth verification for modules
    - Implementation to add global js and css for views
    - Added creation of subcontrollers
    - Added preliminary render of the menu module
    - Added preliminary render of the footer module
    - Added headers for html and json on the responses
    - Added select html tag auto generation inside view
    - Fix for int check and validation inside request class
    - Added trim before string validation
    - Added function to load a global template
    - Simplified callback functions on base view
    - Better system for templates, css and js files.
    - New system for adding custom widgets to the framework
    - Better handling of different files through the framework, like loading css files
      from the main resource directory or from inside a module or widget
    - Added case for xdebug formatting var_dumps on ajax calls
    - Added files for Jenkins CI

v 0.0.1-alpha
    - Added MIT license
    - Added essential classes
    - Fixed name in composer.json
    - Fixed directory structure
    - Removed version from composer.json