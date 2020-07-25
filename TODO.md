# TODO

The following list comprises ideas, suggestions, and known issues, all of which are in consideration for possible implementation in future releases.

***This is not a roadmap or a task list.*** Just because something is listed does not necessarily mean it will ever actually get implemented. Some might be bad ideas. Some might be impractical. Some might either not benefit enough users to justify the effort or might negatively impact too many existing users. Or I may not have the time to devote to the task.

* Add constants to facilitate use of plugin in network mode (+ filter to allow custom overrides)
* Add setting to restrict extended duration settings to admins only (or, rather those enabled via filter or capability - `user_can( 'extended_remember_me_duration' )`
  See: https://wordpress.org/support/topic/feature-request-only-for-admins/
* When saving a setting to enable forever or adjust remember me duration, add admin notice indicating that it only takes affect for subsequent logins? (already noted as such in each setting's help text as of v1.9)
* Style notes about settings that don't take effect until subsequent logins as inline admin notices for more prominence?

Feel free to make your own suggestions or champion for something already on the list (via the [plugin's support forum on WordPress.org](https://wordpress.org/support/plugin/remember-me-controls/) or on [GitHub](https://github.com/coffee2code/remember-me-controls/) as an issue or PR).