# Extensible REDCap Hooks

REDCap supports only one hooks file, specified under REDCap Control Center >
General Configuration > REDCap Hooks. By using the `redcap_hooks.php` file in
this folder, you will essentially be able to use multiple hooks. Furthermore,
hooks can be assigned to a specific project.


## Installation

 1. Move these files to your REDCap server, making note of the full path to the
    `redcap_hooks.php` file.
 2. Open your browser and go to your REDCap Control Center.
 3. Click "General Configuration".
 4. Under "REDCap Hooks", enter the full path to the `redcap_hooks.php` file.


## Adding Hooks

Essentially `redcap_hooks.php` adds a layer of indirection that allows for
multiple implementations per hook. Each hook-function in that file looks for
actual hooks in other PHP files with the same name as the hook.

For example, if you wanted to add functionality when displaying a data entry
form, the hook is called "redcap_data_entry_form". So, you would create a
"redcap_data_entry_form.php" file with your hook-function in it.


## Writing Hook-functions

To avoid name collisions, each hook should be implemented as an [Anonymous
Function](http://php.net/manual/en/functions.anonymous.php)–introduced in PHP
5.3–with the same parameters as the original hook.

For example, here is the entire contents of a `redcap_data_entry_form.php`
file:

	<?php // redcap_data_entry_form.php
	return function ($project_id, $record, $instrument, $event_id, $group_id) {
		print '<script>alert("REDCap Hook Alert!");</script>'
	};


## Project-specific Hooks

If you wanted to have a hook enabled for a specifc project, put your hook's
file under a folder named in the format `pid{$project_id}`, where
`{$project_id}` is the project's REDCap ID.

For example, if you wanted the aforementioned Data Entry hook enabled only for
Project #12, create `pid12/redcap_data_entry_form.php`.


## Additional Hooks

If you have more than one hook, you can create a folder named after the hook
and every PHP files under that folder will be assumed to be a hook file.

For example:

  - `redcap_data_entry_form/print-disclaimer.php`
  - `pid12/redcap_data_entry_form/00-alt-confirm-dialog-hook.php`
  - `pid12/redcap_data_entry_form/01-other-hook.php`
  - `pid12/redcap_data_entry_form/9-more-stuff.php`


## Summary of File Naming Convetion

Hooks are searched for in four places, all relative to the folder in which
`redcap_hooks.php` resides:

 1. Global hook: `$hook_name.php`
 2. Additional global hooks: `$hook_name/*.php`
 3. Project-specific hook: `pid{$project_id}/$hook_name.php`
 4. Additional project-specific hooks: `pid{$project_id}/$hook_name.php`

"Global" hooks are not specific to a project and will run for all projects.

_Caveat:_ PHP's `__DIR__` is used, so take care if using symbolic links.


## Supported Hooks

Every hook except `redcap_custom_verify_username` is supported.

Since `redcap_custom_verify_username` has a non-void return type it has to be
implemented only if needed and directly in `redcap_hooks.php` as per the REDCap
documentation.


## Contributors

 - Taeber Rapczak, University of Florida <taeber@ufl.edu>
 - Philip Chase, University of Florida <pbc@ufl.edu>

Thank you to Andrew Martin (Standford University) for his original work on
custom REDCap Hooks.

## License

Copyright 2015, University of Florida; licensed under the Apache License,
Version 2.0. See the [LICENSE](../LICENSE) file for the full text.
