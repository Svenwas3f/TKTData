/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: April 2020
 * @Purpose: File to manage input selections
 *
 **************** All functions ****************
 * For further description please go to requested function
 *
 * inpuID ()
 *
 * setAttributes ( ele [HTML Element], atts [key and value array] )
 *
 * createHidden ( type [HTML Input type] )
 *
 * removeField ( id [ID of element to be removed] )
 *
 * addMultiple( id [ID of element to provide multiple options (select or radio)] )
 *
 * removeMultiple (formID [Form ID of multiple container], inputID [Input ID of multiple container] )
 *
 * add_text ( type [Name of textinput (text, email, number, date)])
 *
 * add_checkbox ()
 *
 * add_radio ()
 *
 * add_select ()
 *
 * toogleOptions ( ele [HTML Element] )
 *
 * selectElement ( ele [HTML Element] )
 *
 */

/*--------------------------------*/
/* Required functions for the add_ functions
/*--------------------------------*/

function inputID() {
  //Count hidden inputs and return the new input id
  // => Starts at 0
  return ++document.getElementsByName("current_id")[0].value;
}

function setAttributes(ele, atts) {
  //Go throuh every attribute and add it
  for (var key in atts) {
    ele.setAttribute(key, atts[key]);
  }
}

function createHidden(type, id) {
  //Define hidden input
  var hiddenInput = document.createElement("INPUT");
  hiddenInput.setAttribute("type", "hidden");
  hiddenInput.setAttribute("name", "hidden[]");
  hiddenInput.setAttribute("value", type + '%' + id + '%');

  //Reutrn hidden input
  return hiddenInput;
}

function removeField(id) {
  //Remove field
  document.getElementById('container-' + id).remove();
}

function addMultiple(id) {
  //Create multiple element
  var container = document.createElement("DIV")
  var inputId = document.getElementsByName('multiple' + id + '[]').length;
  var multipleInput = document.createElement("INPUT");
  var remove = document.createElement("SPAN");

  //Modifie container
  setAttributes(container, {
    'id': 'multipleContainer-' + id + inputId,
    'class': "multipleContainer"
  })

  //Modifie multipleInput
  input_string( 50, function(string) {
    setAttributes(multipleInput, {
      'type': 'text',
      'name': 'multiple' + id + '[]',
      'placeholder': string, // Name
    });
  });

  //Modifie remove
  setAttributes(remove, {
    'onclick': 'removeMultiple(' + id + ', ' + inputId + ')',
    'style': 'margin: 0px 5px;'
  });

  input_string( 49, function(string) {
    remove.appendChild(document.createTextNode( string ));
  });

  //add multiple
  var div = document.getElementById("container-" + id);

  container.appendChild(multipleInput);
  container.appendChild(remove);

  //Display div
  div.appendChild(container);
}

function removeMultiple(formId, inputId) {
  //Remove element
  document.getElementById('multipleContainer-' + formId + inputId).remove();

}

/*--------------------------------*/
/* Functions to add content to webpage
/*--------------------------------*/

function add_text(type) {
  //Create current id
  var id = inputID();

  /*--------------------------------*/
  /* Create html divs/Inputs
  /*--------------------------------*/
  var name = document.createElement("INPUT");
  var placeholder = document.createElement("INPUT");
  var layout = document.createElement("INPUT");
  var required = document.createElement("INPUT");

  //Set attributes to all
  input_string( 50, function(string) {
    setAttributes(name, {
      'type': 'text',
      'name': 'customField' + id + '[]',
      'placeholder': string, // Name
      'required': 'true'
    });
  });
  input_string( 54, function(string) {
    setAttributes(placeholder, {
      'type': 'text',
      'name': 'customField' + id + '[]',
      'placeholder': string, // Placeholder
      'required': 'true'
    });
  });
  input_string( 51, function(string) {
    setAttributes(layout, {
      'type': 'number',
      'name': 'customField' + id + '[]',
      'placeholder': string, //Order
    });
  });
  setAttributes(required, {
    'type': 'checkbox',
    'name': 'customField' + id + '[]',
    'value': '1'
  });

  //Create structure
  var container = document.createElement("DIV");
  var headline = document.createElement("DIV");
  var topText = document.createElement("H1");
  var remove = document.createElement("SPAN");

  //Modifie container
  setAttributes(container, {
    'class': 'container-custom-form',
    'id': 'container-' + id
  });
  //Modifie topText
  setAttributes(topText, {
    'style': 'display: inline-block'
  });
  input_string( 48, function(string) {
    topText.appendChild(document.createTextNode(type.charAt(0).toUpperCase() + type.slice(1) + '-' + string)); // Wlwmwnr
  });

  //Modiefie Headline
  headline.appendChild(topText);
  //Modifie delete
  setAttributes(remove, {
    'onclick': 'removeField(' + id + ')',
    'style': 'margin: 0px 5px;'
  });

  input_string( 49, function(string) {
    remove.appendChild(document.createTextNode( string )); // Delete
  });

  /*--------------------------------*/
  /* Create content
  /*--------------------------------*/
  var div = document.getElementsByClassName("customFormFields")[0];

  //Append to container
  container.append(createHidden(type, id));
  container.appendChild(headline);
  container.appendChild(name);
  container.appendChild(placeholder);
  container.appendChild(layout);
  container.appendChild(required);

  //Append text
  topText.after(remove);

  input_string( 52, function(string) {
    required.after("(" + string + ")"); // required
  });

  //Append container to div
  //Display full new form
  div.appendChild(container);
}

function add_checkbox() {
  //Create current id
  var id = inputID();

  /*--------------------------------*/
  /* Create html divs/Inputs
  /*--------------------------------*/
  var name = document.createElement("INPUT");
  var layout = document.createElement("INPUT");
  var required = document.createElement("INPUT");

  //Set attributes to all
  input_string( 50, function(string) {
    setAttributes(name, {
      'type': 'text',
      'name': 'customField' + id + '[]',
      'placeholder': string, // Name
      'required': 'true'
    });
  });
  input_string( 51, function(string) {
    setAttributes(layout, {
      'type': 'number',
      'name': 'customField' + id + '[]',
      'placeholder': string, //Order
    });
  });
  setAttributes(required, {
    'type': 'checkbox',
    'name': 'customField' + id + '[]',
    'value': '1'
  });

  //Create structure
  var container = document.createElement("DIV");
  var headline = document.createElement("DIV");
  var topText = document.createElement("H1");
  var remove = document.createElement("SPAN");

  //Modifie container
  setAttributes(container, {
    'class': 'container-custom-form',
    'id': 'container-' + id
  });
  //Modifie topText
  setAttributes(topText, {
    'style': 'display: inline-block'
  });

  input_string( 40, function(string) {
    topText.appendChild(document.createTextNode( string + '-' )); // Checkbox
  });
  input_string( 48, function(string) {
    topText.appendChild(document.createTextNode( string )); // Element
  });

  //Modiefie Headline
  headline.appendChild(topText);
  //Modifie delete
  setAttributes(remove, {
    'onclick': 'removeField(' + id + ')',
    'style': 'margin: 0px 5px;'
  });

  input_string( 49, function(string) {
    remove.appendChild(document.createTextNode( string )); // Delete
  });

  /*--------------------------------*/
  /* Create content
  /*--------------------------------*/
  var div = document.getElementsByClassName("customFormFields")[0];

  //Append to container
  container.append(createHidden('checkbox', id));
  container.appendChild(headline);
  container.appendChild(name);
  container.appendChild(layout);
  container.appendChild(required);

  //Append text
  topText.after(remove);
  input_string( 52, function(string) {
    required.after("(" + string + ")"); // required
  });

  //Append container to div
  //Display full new form
  div.appendChild(container);
}

function add_radio() {
  //Create current id
  var id = inputID();

  /*--------------------------------*/
  /* Create html divs/Inputs
  /*--------------------------------*/
  var name = document.createElement("INPUT");
  var layout = document.createElement("INPUT");
  var required = document.createElement("INPUT");

  //Set attributes to all
  input_string( 50, function(string) {
    setAttributes(name, {
      'type': 'text',
      'name': 'customField' + id + '[]',
      'placeholder': string, // Name
      'required': 'true'
    });
  });
  input_string( 51, function(string) {
    setAttributes(layout, {
      'type': 'number',
      'name': 'customField' + id + '[]',
      'placeholder': string, //Order
    });
  });
  setAttributes(required, {
    'type': 'checkbox',
    'name': 'customField' + id + '[]',
    'value': '1'
  });

  //Create structure
  var container = document.createElement("DIV");
  var headline = document.createElement("DIV");
  var topText = document.createElement("H1");
  var remove = document.createElement("SPAN");
  var add = document.createElement("SPAN");

  //Modifie container
  setAttributes(container, {
    'class': 'container-custom-form',
    'id': 'container-' + id
  });
  //Modifie topText
  setAttributes(topText, {
    'style': 'display: inline-block'
  });

  input_string( 44, function(string) {
    topText.appendChild(document.createTextNode( string + '-' )); // Checkbox
  });
  input_string( 48, function(string) {
    topText.appendChild(document.createTextNode( string )); // Element
  });

  //Modiefie Headline
  headline.appendChild(topText);

  //Modifie delete
  setAttributes(remove, {
    'onclick': 'removeField(' + id + ')',
    'style': 'margin: 0px 5px;'
  });

  input_string( 49, function(string) {
    remove.appendChild(document.createTextNode( string )); // Element
  });

  //Modifie add
  setAttributes(add, {
    'class': 'button',
    'onclick': 'addMultiple(' + id + ')',
    'style': 'margin-bottom: 5px;'
  });

  input_string( 55, function(string) {
    add.appendChild(document.createTextNode( string )); // Element
  });

  /*--------------------------------*/
  /* Create content
  /*--------------------------------*/
  var div = document.getElementsByClassName("customFormFields")[0];

  //Append to container
  container.append(createHidden('radio', id));
  container.appendChild(headline);
  container.appendChild(name);
  container.appendChild(layout);
  container.appendChild(required);
  container.appendChild(document.createElement('br')); //Add blinebreak
  container.appendChild(add);

  //Append text
  topText.after(remove);
  input_string( 52, function(string) {
    required.after("(" + string + ")"); // required
  });

  //Append container to div
  //Display full new form
  div.appendChild(container);

  //Add first multiple
  addMultiple(id);
}

function add_select() {
  //Create current id
  var id = inputID();

  /*--------------------------------*/
  /* Create html divs/Inputs
  /*--------------------------------*/
  var name = document.createElement("INPUT");
  var layout = document.createElement("INPUT");
  var required = document.createElement("INPUT");

  //Set attributes to all
  input_string( 50, function(string) {
    setAttributes(name, {
      'type': 'text',
      'name': 'customField' + id + '[]',
      'placeholder': string, // Name
      'required': 'true'
    });
  });
  input_string( 51, function(string) {
    setAttributes(layout, {
      'type': 'number',
      'name': 'customField' + id + '[]',
      'placeholder': string, //Order
    });
  });
  setAttributes(required, {
    'type': 'checkbox',
    'name': 'customField' + id + '[]',
    'value': '1'
  });

  //Create structure
  var container = document.createElement("DIV");
  var headline = document.createElement("DIV");
  var topText = document.createElement("H1");
  var remove = document.createElement("SPAN");
  var add = document.createElement("SPAN");

  //Modifie container
  setAttributes(container, {
    'class': 'container-custom-form',
    'id': 'container-' + id
  });
  //Modifie topText
  setAttributes(topText, {
    'style': 'display: inline-block'
  });

  input_string( 45, function(string) {
    topText.appendChild(document.createTextNode( string + '-' )); // Checkbox
  });
  input_string( 48, function(string) {
    topText.appendChild(document.createTextNode( string )); // Element
  });

  //Modiefie Headline
  headline.appendChild(topText);

  //Modifie delete
  setAttributes(remove, {
    'onclick': 'removeField(' + id + ')',
    'style': 'margin: 0px 5px;'
  });
  input_string( 49, function(string) {
    remove.appendChild(document.createTextNode( string )); // Remove
  });

  //Modifie add
  setAttributes(add, {
    'class': 'button',
    'onclick': 'addMultiple(' + id + ')',
    'style': 'margin-bottom: 5px;'
  });
  input_string( 55, function(string) {
    add.appendChild(document.createTextNode( string )); // Add
  });

  /*--------------------------------*/
  /* Create content
  /*--------------------------------*/
  var div = document.getElementsByClassName("customFormFields")[0];

  //Append to container
  container.append(createHidden('select', id));
  container.appendChild(headline);
  container.appendChild(name);
  container.appendChild(layout);
  container.appendChild(required);
  container.appendChild(document.createElement('br')); //Add blinebreak
  container.appendChild(add);

  //Append text
  topText.after(remove);
  input_string( 52, function(string) {
    required.after("(" + string + ")"); // required
  });

  //Append container to div
  //Display full new form
  div.appendChild(container);

  //Add first multiple
  addMultiple(id);
}

/* Dropdonw available*/
function toggleOptions(ele) {
  ele.getElementsByClassName("options")[0].classList.toggle("choose");
}

function selectElement(ele) {
  var base = ele.parentNode.parentNode;
  base.getElementsByClassName("headline")[0].innerHTML = ele.textContent;
  base.getElementsByClassName("selectValue")[0].value = ele.getAttribute("data-value");
}

function useNewOption( value, section ) {
  section.getElementsByClassName("selectValue")[0].value = value;
  section.getElementsByClassName("headline")[0].innerHTML = value;

  toggleOptions( section );
}
