function colourScheme() {
  document.getElementById('navbar').style="background-color:" + document.getElementById('brand_colour').value + "; color:" + document.getElementById('text_colour').value + ";";
  document.getElementById('navbar-brand').style="background-color:" + document.getElementById('brand_colour').value + "; color:" + document.getElementById('text_colour').value + ";";
  document.getElementById('navbarDropdown').style="background-color:" + document.getElementById('brand_colour').value + "; color:" + document.getElementById('text_colour').value + ";";
  document.getElementById('card-header').style="background-color:" + document.getElementById('brand_colour').value + "; color:" + document.getElementById('text_colour').value + ";";
  document.getElementById('active').style="background-color:" + document.getElementById('brand_colour').value + "; color:" + document.getElementById('text_colour').value + ";";
  document.getElementById('submit').style="background-color:" + document.getElementById('brand_colour').value + "; color:" + document.getElementById('text_colour').value + ";";
  document.getElementById('reset').style="background-color:" + document.getElementById('brand_colour').value + "; color:" + document.getElementById('text_colour').value + ";";
}

function resetColourScheme() {
  document.getElementById('reset').click();
  colourScheme();
}
