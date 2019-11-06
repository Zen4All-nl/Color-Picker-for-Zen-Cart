# Zen4All Template Color Picker
**!! Currently the module is still in BETA !! It is functional, but still needs some tweaking**
Current version: v1.0.0.beta.1
Zen4All Template Color Picker is a back-end tool to set the colors for a Zen Cart theme, so store owners with no coding experience can do simple color tweaking

On the edit page you can set colors to:
- backgrounds (background-color)
- text (color)
- borders (border-color, border-top-color, border-right-color, border-bottom-color, border-left-color)


## Instalation
- Copy all the files to your server.
- the auto installer will add some values to your database:
  - menu items
  - mod version
  - a configuration field to set the file name for the color stylesheet
  
## Usage
- Go to admin->configuration->Templat Colors
- Edit the value of the stylesheet name, if needed
- Go to admin->tools->Template Colors
- Select the template from the dropdown, and the values of your color stylesheet apera
- Now you can edit, delete, or add a new property

## Todo, and limitations
- [ ] Adding a new css element (v1.0.0.beta.2)
- [ ] Deleting a css element (v1.0.0.beta.2)
- [ ] Add proper documentation (v1.0.0)
- [ ] Replace hardcoded texts with language defines (v1.0.0.beta.2)
- [ ] Add titles to buttons (v1.0.0.beta.2)
- [ ] Add ajax, so page reload without jumping back to top after clikcing a button (v2.0.0)

You can not use fancy css slectors like: .rating > input:checked + label:hover
The + < > ~ signs are ingored.  This may de fixed in the future