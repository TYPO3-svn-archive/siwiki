/**
 * combines all necessary js scripts for siwiki
 *
 * updatelocking.js
 * wikilinks.js
 *
 * thanks to www.webtoolkit.info
 * http://www.webtoolkit.info/javascript-md5.html
 * md5.js
 * 
 * thanks to allmybrain.com
 * http://allmybrain.com/2007/10/16/an-image-upload-extension-for-yui-rich-text-editor/
 * yui-image-uploader.js
 */

/**
 * updatelocking.js
 */
function updateLocking(rte,urlUpdate,urlCancel,interval,articleHash) {	
    //time minutes to miliseconds
    //minus tolerance to avoid running condition
    interval = (interval * 60 * 1000)*0.95;
            var Dom = YAHOO.util.Dom,
                Event = YAHOO.util.Event;

                rte.on('toolbarLoaded', function() {

                        var timer = {
                                count :0,
                                'updateLocking' : function(data) {
                                        this.count++;
                                        rte.saveHTML();
					if(articleHash == MD5(Dom.get('siwiki_article').value)){
        			       		window.location.href = urlCancel;	
					} else {
						articleHash = MD5(Dom.get('siwiki_article').value);					
        			       		YAHOO.util.Connect.asyncRequest('GET',urlUpdate, callbacks);	
					}
                                }
                        }
        		YAHOO.lang.later(interval, timer, 'updateLocking',[{"data":"foo"}], true);
                });

                //async callback 
                  var callbacks = {
                                failure:function(o) {
                                        if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                //console.log('ajax connection failed');
                                        }
                                }
                   }; // end callbacks   
}

/**
 * wikilinks.js
 */
function wikilink(rte, asyncUrl, defaultNamespace) {	
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event,
		currentNamespace = defaultNamespace;
    rte.on('windowCreateLinkRender', function() {
        var body = this._windows.createlink.body;    
        var label1 = document.createElement('label');
        label1.innerHTML = '<strong>Wiki Link:</strong>'+
                            '<input type="text" id="' +
                            rte.get('id')+'_wikilink_url" name="wikilink_url" size="10" style="width: 200px" value="" />'+
                            '</label>';
        var label2 = document.createElement('label');
        label2.innerHTML = '<strong>Wiki Category:</strong><div id="'+rte.get('id')+'_dropDownMenu'  + '"></div></label>';
        var _elem = Dom.get(this.get('id') + '_createlink_url');
        Dom.insertBefore(label1, _elem.parentNode);
        Dom.insertBefore(label2, _elem.parentNode);
        
        // create link to switch to the default view
        var switchLinks=document.createElement('div');
            switchLinks.innerHTML=	'<a href="#" id="showDefaultLink">Insert external link</a><a href="#" id="showWikiLink">Insert wiki link</a>';
            switchLinks.style.cssFloat = 'left';
  		var _elem=Dom.get(rte.get('id') + '_createlink_title');					
        Dom.insertAfter(switchLinks,_elem.parentNode); 

	                               
        //This stops the menu's A's from bubbling the click
        Event.on(rte.get('id')+'_dropDownMenu', 'click', function(ev) {
            Event.stopEvent(ev);
        });      
    });
    
    rte.on('afterOpenWindow', function(args) {
    
	        if (args.win.name == 'createlink') {
	        	// get all neccessary elements
	          	var linkUrl = Dom.get(rte.get('id') + '_createlink_url'),
	             	linkTarget = Dom.get(rte.get('id') + '_createlink_target'),
	              	linkTitle = Dom.get(rte.get('id') + '_createlink_title'),      
	               	wikilinkUrl = Dom.get(rte.get('id') + '_wikilink_url'),  
	               	wikilinkNamespaces = Dom.get(rte.get('id') + '_dropDownMenu'),      
	             	showDefaultLink = Dom.get('showDefaultLink'),	
	             	showWikiLink = Dom.get('showWikiLink');

			Event.on(wikilinkUrl,'keydown',function(ev) {
				if((ev.keyCode < 48 || ev.keyCode > 105 ) && ev.keyCode != 8 && ev.keyCode != 109 && ev.keyCode != 189 ){
		            		Event.stopEvent(ev);
				}
			});
	             	
	        	Event.on(showDefaultLink, 'click', function(ev) { 	          
	            		Event.stopEvent(ev);
	          
				linkUrl.value = '';	
				linkTitle.value = '';	
				linkUrl.parentNode.style.display = 'block';                    		
				linkTarget.parentNode.style.display = 'block';
				linkTitle.parentNode.style.display = 'block';	
	             	 
				wikilinkUrl.value = '';
				wikilinkUrl.parentNode.style.display = 'none';
				wikilinkNamespaces.parentNode.style.display = 'none';
				showDefaultLink.style.display = 'none';        
				showWikiLink.style.display = 'block';                   		
			});
			
			//listener for showWikiLink
			Event.on(showWikiLink, 'click', function(ev) {           
	            		Event.stopEvent(ev);
				linkUrl.value = '';	
				linkTitle.value = '';	
				linkUrl.parentNode.style.display = 'none';                    		
				linkTarget.parentNode.style.display = 'none';
				linkTitle.parentNode.style.display = 'none';	
				 
				wikilinkUrl.value = '';
				wikilinkUrl.parentNode.style.display = 'block';
				wikilinkNamespaces.parentNode.style.display = 'block';
				showDefaultLink.style.display = 'block';        
				showWikiLink.style.display = 'none';                       		
			});
		
            //CreateLink panel was opened, update the Menu...
            YAHOO.util.Connect.asyncRequest('GET', asyncUrl, callbacks);
				if(this.browser.ie){
        				var el = rte._getSelectedElement();
				} else {
					var el = rte.currentElement[0];
				}
        			url = el.getAttribute("href");
        			if(url){
	        			var _url = url.split("://");
	        			if(_url[0] == "wiki"){
	        				var _url = _url[1].split("@");
	        				wikilinkUrl.value = decodeURIComponent(_url[0]);
	        				// hide default link attributes 
                        			linkUrl.parentNode.style.display = 'none';
                        			linkTarget.parentNode.style.display = 'none';
                        			linkTitle.parentNode.style.display = 'none';
	        				showDefaultLink.style.display = 'block';
	        				showWikiLink.style.display = 'none';
	        			} else {
	        				wikilinkUrl.parentNode.style.display = 'none';  
	        				wikilinkNamespaces.parentNode.style.display = 'none';
	        				linkUrl.parentNode.style.display = 'block';                    		
                        			linkTarget.parentNode.style.display = 'block';
                        			linkTitle.parentNode.style.display = 'block';
	        				showDefaultLink.style.display = 'none';
	        				showWikiLink.style.display = 'block';
	        			}
	        		} else {
	        		        // hide default link attributes 
                        		linkUrl.parentNode.style.display = 'none';
                        		linkTarget.parentNode.style.display = 'none';
                        		linkTitle.parentNode.style.display = 'none';

					// show wiki link attributes
					wikilinkUrl.parentNode.style.display = 'block';  
					wikilinkNamespaces.parentNode.style.display = 'block';

					showDefaultLink.style.display = 'block';
					showWikiLink.style.display = 'none';

	        			// new wiki link
					var linkname = el.innerHTML.replace(/<a>/,"").replace(/<\/a>/,"").replace(/\s/,"_");
					wikilinkUrl.value = decodeURIComponent(linkname);
	        		}		
        }
    });
    
    rte.on('windowcreatelinkClose', function() {
    	 var wikilinkUrl = Dom.get(rte.get('id') + '_wikilink_url'),
    	 	 linkUrl = Dom.get(rte.get('id') + '_createlink_url'),	
         	 linkTitle = Dom.get(rte.get('id') + '_createlink_title');
		 if(wikilinkUrl.value != ""){ 
       		 	linkUrl.value = "wiki:/"+"/"+wikilinkUrl.value+"@"+currentNamespace;  	 	   	 	
    	 	 	linkTitle.value = wikilinkUrl.value+"@"+currentNamespace;   
		 }
    });

    function createDropDownMenu(namespaces) {
        if (Dom.get(rte.get('id')+'_dropDownMenu')) {
            //Wipe the old button
            Dom.get(rte.get('id')+'_dropDownMenu').innerHTML = '';
        }
	//console.log(namespaces);
        var menuItems = [];
        for (var i = 0, len = namespaces.length; i < len; ++i) {
            var m = namespaces[i];
            menuItems[i] = {
                text: m.name,
                value: m.name
            };
	    if(m.uid == currentNamespace) currentNamespace = m.name;
        }
        
        //select current namespace
        var el = rte.currentElement[0];
	if(el) var url = el.getAttribute("href");
        if(url !== null){
         	var _url = url.split("://");
		if(_url[0] == "wiki"){
			var _url = _url[1].split("@");
			currentNamespace = decodeURIComponent(_url[1].replace(/\//,""));
		} 
        } 

        var dropDownMenu = new YAHOO.widget.Button({
            type: "menu",
            label: currentNamespace,
            name: "menuItems",
            menu: menuItems,
            container: rte.get('id')+'_dropDownMenu'
        });
        
        dropDownMenu.getMenu().mouseUpEvent.subscribe(function(ev, args) {
            Event.stopEvent(args[0]);
            dropDownMenu.set("label", args[1].cfg.getProperty("text"));
            currentNamespace = args[1].cfg.getProperty("text");
            dropDownMenu._hideMenu();
        });
    } 
    
    //async callback 
	  var callbacks = {
			success : function(o) {
					try {
						messages = YAHOO.lang.JSON.parse(o.responseText);
					}
					catch (e){
						////console.log('JSON Parse failed '+e);
						return;
					}		
					createDropDownMenu(messages);
					// Assume we got some data	
			 },
			failure:function(o) {
				if(!YAHOO.util.Connect.isCallInProgress(o)) {
					//console.log('ajax connection failed');
				}
		  	}
	   }; // end callbacks   
	   
}

/**
 *  md5.js
 *  MD5 (Message-Digest Algorithm)
 *  http://www.webtoolkit.info/
 *
 */
var MD5 = function (string) {

    function RotateLeft(lValue, iShiftBits) {
        return (lValue<<iShiftBits) | (lValue>>>(32-iShiftBits));
    }

    function AddUnsigned(lX,lY) {
        var lX4,lY4,lX8,lY8,lResult;
        lX8 = (lX & 0x80000000);
        lY8 = (lY & 0x80000000);
        lX4 = (lX & 0x40000000);
        lY4 = (lY & 0x40000000);
        lResult = (lX & 0x3FFFFFFF)+(lY & 0x3FFFFFFF);
        if (lX4 & lY4) {
            return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
        }
        if (lX4 | lY4) {
            if (lResult & 0x40000000) {
                return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
            } else {
                return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
            }
        } else {
            return (lResult ^ lX8 ^ lY8);
        }
     }

     function F(x,y,z) { return (x & y) | ((~x) & z); }
     function G(x,y,z) { return (x & z) | (y & (~z)); }
     function H(x,y,z) { return (x ^ y ^ z); }
    function I(x,y,z) { return (y ^ (x | (~z))); }

    function FF(a,b,c,d,x,s,ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    };

    function GG(a,b,c,d,x,s,ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    };

    function HH(a,b,c,d,x,s,ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    };

    function II(a,b,c,d,x,s,ac) {
        a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
        return AddUnsigned(RotateLeft(a, s), b);
    };

    function ConvertToWordArray(string) {
        var lWordCount;
        var lMessageLength = string.length;
        var lNumberOfWords_temp1=lMessageLength + 8;
        var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1 % 64))/64;
        var lNumberOfWords = (lNumberOfWords_temp2+1)*16;
        var lWordArray=Array(lNumberOfWords-1);
        var lBytePosition = 0;
        var lByteCount = 0;
        while ( lByteCount < lMessageLength ) {
            lWordCount = (lByteCount-(lByteCount % 4))/4;
            lBytePosition = (lByteCount % 4)*8;
            lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount)<<lBytePosition));
            lByteCount++;
        }
        lWordCount = (lByteCount-(lByteCount % 4))/4;
        lBytePosition = (lByteCount % 4)*8;
        lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80<<lBytePosition);
        lWordArray[lNumberOfWords-2] = lMessageLength<<3;
        lWordArray[lNumberOfWords-1] = lMessageLength>>>29;
        return lWordArray;
    };

    function WordToHex(lValue) {
        var WordToHexValue="",WordToHexValue_temp="",lByte,lCount;
        for (lCount = 0;lCount<=3;lCount++) {
            lByte = (lValue>>>(lCount*8)) & 255;
            WordToHexValue_temp = "0" + lByte.toString(16);
            WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length-2,2);
        }
        return WordToHexValue;
    };

    function Utf8Encode(string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    };

    var x=Array();
    var k,AA,BB,CC,DD,a,b,c,d;
    var S11=7, S12=12, S13=17, S14=22;
    var S21=5, S22=9 , S23=14, S24=20;
    var S31=4, S32=11, S33=16, S34=23;
    var S41=6, S42=10, S43=15, S44=21;

    string = Utf8Encode(string);

    x = ConvertToWordArray(string);

    a = 0x67452301; b = 0xEFCDAB89; c = 0x98BADCFE; d = 0x10325476;

    for (k=0;k<x.length;k+=16) {
        AA=a; BB=b; CC=c; DD=d;
        a=FF(a,b,c,d,x[k+0], S11,0xD76AA478);
        d=FF(d,a,b,c,x[k+1], S12,0xE8C7B756);
        c=FF(c,d,a,b,x[k+2], S13,0x242070DB);
        b=FF(b,c,d,a,x[k+3], S14,0xC1BDCEEE);
        a=FF(a,b,c,d,x[k+4], S11,0xF57C0FAF);
        d=FF(d,a,b,c,x[k+5], S12,0x4787C62A);
        c=FF(c,d,a,b,x[k+6], S13,0xA8304613);
        b=FF(b,c,d,a,x[k+7], S14,0xFD469501);
        a=FF(a,b,c,d,x[k+8], S11,0x698098D8);
        d=FF(d,a,b,c,x[k+9], S12,0x8B44F7AF);
        c=FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);
        b=FF(b,c,d,a,x[k+11],S14,0x895CD7BE);
        a=FF(a,b,c,d,x[k+12],S11,0x6B901122);
        d=FF(d,a,b,c,x[k+13],S12,0xFD987193);
        c=FF(c,d,a,b,x[k+14],S13,0xA679438E);
        b=FF(b,c,d,a,x[k+15],S14,0x49B40821);
        a=GG(a,b,c,d,x[k+1], S21,0xF61E2562);
        d=GG(d,a,b,c,x[k+6], S22,0xC040B340);
        c=GG(c,d,a,b,x[k+11],S23,0x265E5A51);
        b=GG(b,c,d,a,x[k+0], S24,0xE9B6C7AA);
        a=GG(a,b,c,d,x[k+5], S21,0xD62F105D);
        d=GG(d,a,b,c,x[k+10],S22,0x2441453);
        c=GG(c,d,a,b,x[k+15],S23,0xD8A1E681);
        b=GG(b,c,d,a,x[k+4], S24,0xE7D3FBC8);
        a=GG(a,b,c,d,x[k+9], S21,0x21E1CDE6);
        d=GG(d,a,b,c,x[k+14],S22,0xC33707D6);
        c=GG(c,d,a,b,x[k+3], S23,0xF4D50D87);
        b=GG(b,c,d,a,x[k+8], S24,0x455A14ED);
        a=GG(a,b,c,d,x[k+13],S21,0xA9E3E905);
        d=GG(d,a,b,c,x[k+2], S22,0xFCEFA3F8);
        c=GG(c,d,a,b,x[k+7], S23,0x676F02D9);
        b=GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);
        a=HH(a,b,c,d,x[k+5], S31,0xFFFA3942);
        d=HH(d,a,b,c,x[k+8], S32,0x8771F681);
        c=HH(c,d,a,b,x[k+11],S33,0x6D9D6122);
        b=HH(b,c,d,a,x[k+14],S34,0xFDE5380C);
        a=HH(a,b,c,d,x[k+1], S31,0xA4BEEA44);
        d=HH(d,a,b,c,x[k+4], S32,0x4BDECFA9);
        c=HH(c,d,a,b,x[k+7], S33,0xF6BB4B60);
        b=HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);
        a=HH(a,b,c,d,x[k+13],S31,0x289B7EC6);
        d=HH(d,a,b,c,x[k+0], S32,0xEAA127FA);
        c=HH(c,d,a,b,x[k+3], S33,0xD4EF3085);
        b=HH(b,c,d,a,x[k+6], S34,0x4881D05);
        a=HH(a,b,c,d,x[k+9], S31,0xD9D4D039);
        d=HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);
        c=HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);
        b=HH(b,c,d,a,x[k+2], S34,0xC4AC5665);
        a=II(a,b,c,d,x[k+0], S41,0xF4292244);
        d=II(d,a,b,c,x[k+7], S42,0x432AFF97);
        c=II(c,d,a,b,x[k+14],S43,0xAB9423A7);
        b=II(b,c,d,a,x[k+5], S44,0xFC93A039);
        a=II(a,b,c,d,x[k+12],S41,0x655B59C3);
        d=II(d,a,b,c,x[k+3], S42,0x8F0CCC92);
        c=II(c,d,a,b,x[k+10],S43,0xFFEFF47D);
        b=II(b,c,d,a,x[k+1], S44,0x85845DD1);
        a=II(a,b,c,d,x[k+8], S41,0x6FA87E4F);
        d=II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);
        c=II(c,d,a,b,x[k+6], S43,0xA3014314);
        b=II(b,c,d,a,x[k+13],S44,0x4E0811A1);
        a=II(a,b,c,d,x[k+4], S41,0xF7537E82);
        d=II(d,a,b,c,x[k+11],S42,0xBD3AF235);
        c=II(c,d,a,b,x[k+2], S43,0x2AD7D2BB);
        b=II(b,c,d,a,x[k+9], S44,0xEB86D391);
        a=AddUnsigned(a,AA);
        b=AddUnsigned(b,BB);
        c=AddUnsigned(c,CC);
        d=AddUnsigned(d,DD);
    }

    var temp = WordToHex(a)+WordToHex(b)+WordToHex(c)+WordToHex(d);

    return temp.toLowerCase();
}

/**
 * yui-image-uploader.js
 */
function yuiImgUploader(rte, upload_url, upload_image_name) {
   // customize the editor img button 
   YAHOO.log( "Adding Click Listener" ,'debug');
   
   rte.addListener('toolbarLoaded',function() {
       rte.toolbar.addListener ( 'insertimageClick', function(o) {
           try {
               var imgPanel=new YAHOO.util.Element(rte.get('id') + '-panel');
               imgPanel.on ( 'contentReady', function() {
                   try {
                       var Dom=YAHOO.util.Dom;

                       if (! Dom.get(rte.get('id')+ '_insertimage_upload'))
                       {
                           var label=document.createElement('label');
                          label.innerHTML='<strong>Upload:</strong>'+
			         '<input type="file" id="' +
				  rte.get('id') + '_insertimage_upload" name="'+upload_image_name+
			         '" size="10" style="width: 300px" />'+
			         '</label>';

                           var img_elem=Dom.get(rte.get('id') + '_insertimage_url');
                           Dom.getAncestorByTagName(img_elem, 'form').encoding = 'multipart/form-data';

                           Dom.insertAfter(
                               label,
                               img_elem.parentNode);

                           YAHOO.util.Event.on (rte.get('id') + '_insertimage_upload', 'change', function(ev) {
                               YAHOO.util.Event.stopEvent(ev); // no default click action
                               YAHOO.util.Connect.setForm ( img_elem.form, true, true );
                                  YAHOO.util.Connect.asyncRequest('POST', upload_url, 
					  { 
                                           upload:function(r){
                                               try {
                                                   // strip pre tags if they got added somehow
                                                   //resp=r.responseText.replace( /<pre>/i, '').replace( /<\/pre>/i, '');
                                                  // //console.log(resp);
                                                  // var o=eval('('+resp+')');
                                                   var result = YAHOO.lang.JSON.parse(r.responseText);
                                                   if (result[0]['status']=='UPLOADED') {
                                                       Dom.get(rte.get('id') + '_insertimage_upload').value='';
                                                       Dom.get(rte.get('id') + '_insertimage_url').value=decodeURIComponent(result[1]['image_url']);
                                                       // tell the image panel the url changed
                                                       // hack instead of fireEvent('blur')
                                                       // which for some reason isn't working
                                                       Dom.get(rte.get('id') + '_insertimage_url').focus();
                                                       Dom.get(rte.get('id') + '_insertimage_upload').focus();
                                                   } else {
                                                       alert( "Upload Failed: "+result[0]['status']);
                                                   }
                                               } catch ( eee ) {
                                                  // console.log( eee.message, 'error' );
                                               }
                                   }
                               }
                               );
                               return false;
                           });
                       }
                   }
			catch ( ee ) { YAHOO.log( ee.message, 'error' ); }
		   
               });
           } catch ( e ) {
               YAHOO.log( e.message, 'error' );
           }
       });
   });

}
