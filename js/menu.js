/**
 ************* General *************
 * @Author: Sven Waser
 * @System: TKTData
 * @Version: 1.0
 * @Published: July 2020
 * @Purpose: File to manage menu
 *
 **************** All functions ****************
 * For further description please go to requested function
 *
 * openMenu( mainPage [main page id] )
 *
 */

function openMenu(mainPage) {
  //Define variables
  var subMenus = document.getElementsByClassName("subOpen");
  var mainMenus = document.getElementsByClassName("mainpage" + mainPage)

  //Add hidden
  for (var i = 0; i < subMenus.length; i++) {
    subMenus[i].classList.add("hidden");
  }

  //Remove open
  for (var i = 0; i < subMenus.length; i++) {
    subMenus[0].classList.remove("subOpen");
  }

  //Open menus
  for (var i = 0; i < mainMenus.length; i++) {
    mainMenus[i].classList.remove("hidden");
    mainMenus[i].classList.add("subOpen");
  }

}