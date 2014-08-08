var uF = {
	
	checkAttr: function(elem) {
		if(elem.getAttribute == undefined) {
			return(false);
			}
		else {
			if(elem.getAttribute("data-uf") == undefined) {
				return(false);
				}
			else {
				return(true);
				}
			}
		},
		
	getRoot: function(elem) {
		if(uF.checkAttr(elem)) {
			var uFPath = elem.getAttribute("data-uf").split(":");
			var pathLen = uFPath.length;
			if(uFPath[0] == "") {
				return(uF.getRoot(elem.parentNode));
				}
			else {
				return(uFPath[0]);
				}
			}
		else {
			return(undefined);
			}
		},
	
	buildObj: function(elem) {
		var wObj = {};
		wObj["uFType"] = uF.getRoot(elem);
		var children = elem.childNodes;
		for(var childNum=0,l=children.length;childNum<l;childNum++) {
			uF.buildForNode(children[childNum],wObj,wObj["uFType"]);
			}
		uF.cleanUpObj(wObj);
		return(wObj)
		},
	
	cleanUpObj: function(obj) {
		for(entry in obj) {
			if((obj[entry]["uf-temp-data"] == undefined) && (entry != "uFType")) {
				uF.cleanUpObj(obj[entry]);
				}
			else {
				obj[entry] = obj[entry]["uf-temp-data"];
				}
			}
		},
	
	buildForNode: function(elem,currentRef,name) {
		if(elem.nodeType == 1) {//elem is elementNode
			var children = elem.childNodes;
			var uFPath;
			if(uF.checkAttr(elem)) {
				uFPath = elem.getAttribute("data-uf").split(":");
				if(uFPath[0] == "") {
					uFPath[0] = name;
					}
				var pathIdx = uFPath.indexOf(name)+1;
				for(var l=uFPath.length;pathIdx<l;pathIdx++) {
					if(currentRef[uFPath[pathIdx]] == undefined) {
						currentRef[uFPath[pathIdx]] = {};
						}
					currentRef = currentRef[uFPath[pathIdx]];
					}
				for(var childNum=0,l=children.length;childNum<l;childNum++) {
					uF.buildForNode(children[childNum],currentRef,uFPath[uFPath.length - 1]);
					}
				}
			else {
				for(var childNum=0,l=children.length;childNum<l;childNum++) {
					uF.buildForNode(children[childNum],currentRef,name);
					}
				}
			}
		else {
			if(elem.nodeType == 3 && elem.nextSibling == undefined && elem.previousSibling == undefined) {//elem is "onlyChild" textNode (i.e. within span tag)
				currentRef["uf-temp-data"] = String(elem.data);
				}
			}
		}

	}