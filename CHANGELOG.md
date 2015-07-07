# Change Log
All notable changes to this project will be documented in this file.
The version numbers shown here are a two part version number.  The first part
is the REDCap version number against which tests are run.  The second part is
the version of the hooks and their tests.  These version nunmbers adhere to [Semantic Versioning](http://semver.org/).


## [6.5.3-0.1.0] - 2015-07-07
### Added
- Add Travis-CI testing (Philip Chase)
- New Hooks (Taeber Rapczak)
- Set the REDCap zip used to 6.5.3 (Taeber Rapczak)

### Removed
- Remove old hooks (Taeber Rapczak)


## [0.0.3] - 2015-04-30
### Added
- Add a Change Log compliant with http://keepachangelog.com/
- Add selenium scratch files to .gitignore
- Add ./scripts/ folder and scripts to create a project and load a data dictionary
- Add docmentation on selenium prerequisites and project creation scripts
- Add url rewriting plugin in ./plugins
- Add the BSD license text to the ./vagrant folder

### Changed
- Vagrant VM now links ./plugins/ and ./hooks/ into REDCap's default location


## [0.0.2] - 2015-01-20
### Added
 - Add a REDCap testing VM based on Vagrant (Philip Chase)


## [0.0.1] - 2015-01-20
### Added
 - Update README.md (David Rogers)
 - Initial beta of imageview function (123andy)
 - Adding common utility functions (123andy)
 - Making site public (123andy)
 - Added beta version of custom media player hook (123andy)
 - moved hooks config file into hooks directory (123andy)
 - hiding checkbox options on imagemap question (123andy)
 - project-specific redcap_survey_hook (123andy)
 - Initial version of REDCap Survey Hooks (123andy)
 - Initial commit (garricko)
