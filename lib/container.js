function containerDisplaySwitching(id)
{
    var id_cont = id + "_cont";
	var x = new getObj(id_cont);
    var what = (x.style.display == 'inline' || x.style.display == '') ? 'none' : 'inline';
    x.style.display = what;
    new cookie(id, what, 356, '/').set();
}

function containerDisplaySet(id)
{
    var id_cont = id + "_cont";
	var x = new getObj(id_cont);
    var what = new cookie(id).read();
    if (what != null) {
        x.style.display = what;
    }
}

function getObj(id)
{
  if (document.getElementById)
  {
  	this.obj = document.getElementById(id);
	this.style = document.getElementById(id).style;
  }
  else if (document.all)
  {
	this.obj = document.all[id];
	this.style = document.all[id].style;
  }
  else if (document.layers)
  {
   	this.obj = document.layers[id];
   	this.style = document.layers[id];
  }
}
