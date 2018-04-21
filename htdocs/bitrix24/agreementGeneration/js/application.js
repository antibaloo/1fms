function frameResize(){
  var currentSize = BX24.getScrollSize();
  minHeight = currentSize.scrollHeight;
  if (minHeight < 400) minHeight = 400;
  BX24.resizeWindow(document.getElementById("app").offsetWidth, minHeight);
}