<?php
/**
 *
 * renders all templates besides updates
 *
 * @author Stefan Isak <stefan.isak@googlemail.com>
 * @author Andreas Lappe <nd@off-pist.de>
 * @package TYPO3
 * @subpackage tx_siwiki
 * @version $Id: class.tx_siwiki_views_siwiki.php 1248 2009-07-09 09:36:57Z alappe $
 *
 */ 
class tx_siwiki_views_siwiki extends tx_lib_phpTemplateEngine {

        /**
         * Render JSON output for AJAX (YUI)
         *
         * @param string $view
         * @return string
         */
        function renderJSON($view) {
                $view = parent::render($view);
                return $view;
        }

        /**
         * Render PNG
         *
         * @param string $view
         * @return string
         */
        function renderPNG($view) {
                $view = parent::render($view);
                return $view;
        }

        function renderToc($view) {
                $view = parent::render($view);
                return $view;
        }
        
        function printAsImage($str) {
        $image = tx_div::makeInstance('tx_lib_image');
                $image->path($str);
                print $image->make();
        }

        /**
         * Render the article as html and wrap into div
         *
         * @param string $article
         */
    function printAsHtml($article) {
        print '<div class="siwiki-article">';
            parent::printAsHtml($article);
        print '</div>'; 
    }

        /**
         * Render the Yahoo! User Interface Rich Text Editor with the article
         *
         * @param string $title
         * @param int $namespace
         * @param string $article
         */
    function printAsYuiRte($title, $namespace, $article){
        $this->includeYuiRte($title, $namespace, md5($article));    
            print '<textarea name="siwiki[article]" id="siwiki_article" cols="50" rows="10">'.$article.'</textarea>'; 
    }


        function createSearchBox(){
        $destination = $GLOBALS['TSFE']->id . ',' . $this->controller->configurations->get('ajaxPageType');  
        $link = tx_div::makeInstance('tx_lib_link');
        $link->destination($destination);
        $link->designator($this->getDesignator());
                if($this->controller->configurations->get('fullTextSearch')){
                        $link->parameters(array('action' => 'ajax',
                                                'request' => 'getFullTextSearchResult'));
                } else {
                        $link->parameters(array('action' => 'ajax',
                                                'request' => 'getSearchResult'));
                }
                        
        $link->noHash();

        $searchLink = tx_div::makeInstance('tx_lib_link');
        $searchLink->destination($this->getDestination());
        $searchLink->designator($this->getDesignator());
        $searchLink->noHash();
                $searchLink = $searchLink->makeUrl(false);

                $box = '<div id="siwiki-search"><input id="siwiki-searchbox" type="text" value="%%%searchlabel%%%" /><div id="siwiki-autocomplete"></div></div>';
                $box .= '<script type="text/javascript"> 
                                var searchbox = YAHOO.util.Dom.get("siwiki-searchbox");
                                YAHOO.util.Event.addListener(searchbox,"focus",function() { searchbox.value = "" });

                                var autoComplete = function(){        

                                        var myDataSource = new YAHOO.util.XHRDataSource("'.$link->makeUrl(false).'");

                                        myDataSource.responseType = YAHOO.util.XHRDataSource.TYPE_JSON; 
                                        myDataSource.responseSchema = { 
                                                        resultsList : "results", 
                                                        fields : ["title","uid","namespace"]  
                                        }
                                        var myAutoComp = new YAHOO.widget.AutoComplete("siwiki-searchbox","siwiki-autocomplete", myDataSource); 
                                        myAutoComp.supressInputUpdate = true;
                                        myAutoComp.queryQuestionMark = false;

                                        myAutoComp.animVert = true;
                                        myAutoComp.animHoriz = false;
                                        myAutoComp.animSpeed = 0.2;
                                        myAutoComp.maxResultsDisplayed = 15;
                                        myAutoComp.useShadow = false;
                                        myAutoComp.minQueryLength = 1;  
                                        myAutoComp.queryDelay = 0.1; 

                                        myAutoComp.generateRequest = function(sQuery) {
                                                    return "&'.$this->getDesignator().'[query]=" + sQuery;
                                        };
                                        
                                        var itemSelectHandler = function(sType, aArgs) {
                                                window.location.href = "'.$searchLink.'&'.$this->getDesignator().'[namespace]="+aArgs[2][2]+"&'.$this->getDesignator().'[uid]="+aArgs[2][1];
                                        };

                                        myAutoComp.itemSelectEvent.subscribe(itemSelectHandler);

                                        return {
                                              myDataSource: myDataSource,
                                              myAutoComp: myAutoComp
                                        }
                                }();
                                
                                                                
                         </script>'; 
                return $box;
        }

        function printToolbar($which){
                print '<div id="siwiki-toolbar">';
                        switch($which){
                                case 'display':
                                        $this->printMenuItems();
                                        $this->printTags();
                                break;
                                case 'edit':
                                        $this->printEditMenuItems();
                                        $this->printTags();
                                break;
                                case 'diff':
                                        $this->printDiffMenuItems();
                                break;
                                case 'new':
                                        $this->printNewMenuItems();
                                break;
                        }
                print '</div>';
        }

        function printTags(){
        $destination = $GLOBALS['TSFE']->id . ',' . $this->controller->configurations->get('ajaxPageType');  
        $link = tx_div::makeInstance('tx_lib_link');
        $link->destination($destination);
        $link->designator($this->getDesignator());
                $link->parameters(array('namespace' => $this->controller->parameters->get('namespace'),
                                        'uid' => $this->controller->parameters->get('uid'),
                                        'action' => 'ajax',
                                        'request' => 'getTags'));
        $link->noHash();

        $link2 = tx_div::makeInstance('tx_lib_link');
        $link2->destination($destination);
        $link2->designator($this->getDesignator());
                $link2->parameters(array('namespace' => $this->controller->parameters->get('namespace'),
                                        'uid' => $this->controller->parameters->get('uid'),
                                        'action' => 'ajax',
                                        'request' => 'getArticlesByTag'));
        $link2->noHash();

        $link3 = tx_div::makeInstance('tx_lib_link');
        $link3->destination($this->getDestination());
        $link3->designator($this->getDesignator());
        $link3->noHash();

        $link4 = tx_div::makeInstance('tx_lib_link');
        $link4->destination($destination);
        $link4->designator($this->getDesignator());
                $link4->parameters(array('namespace' => $this->controller->parameters->get('namespace'),
                                        'uid' => $this->controller->parameters->get('uid'),
                                        'action' => 'ajax',
                                        'request' => 'setTag',
                                        'tag' => ''));
        $link4->noHash();

        $link5 = tx_div::makeInstance('tx_lib_link');
        $link5->destination($destination);
        $link5->designator($this->getDesignator());
                $link5->parameters(array('namespace' => $this->controller->parameters->get('namespace'),
                                        'uid' => $this->controller->parameters->get('uid'),
                                        'action' => 'ajax',
                                        'request' => 'unsetTag',
                                        'tid' => ''));
        $link5->noHash();

                print '<div id="siwiki-tags"><div id="siwiki-panel-tags"></div><div id="siwiki-tag-context"></div></div>
                        <script type="text/javascript">
                                var Dom = YAHOO.util.Dom,
                                    Event = YAHOO.util.Event,
                                    articles = null,
                                    articleSource = null,
                                    articleTable = null,
                                    tagPanel = null,
                                    tagMenuButton = null,
                                    tagTable = null,
                                    countRows = 0,
                                    tags = null;
                                
                                //initially get all tags for current article
                                YAHOO.util.Connect.asyncRequest("GET", "'.$link->makeUrl(false).'", 
                                           {
                                                success : function(o) {
                                                        try {
                                                           tags = YAHOO.lang.JSON.parse(o.responseText);
                                                        }
                                                        catch (e) {
                                                                //console.log("json parse error: e");
                                                        }
                                                        var tagMenuButtonLabel = "";
                                                        if(tags !== null){
                                                                for(i in tags){
                                                                        tagMenuButtonLabel += tags[i].tag + ", ";
                                                                }
                                                        } else {
                                                                tagMenuButtonLabel = "%%%noTag%%%";
                                                        }
                                                        renderPanel();
                                                        tagMenuButton = new YAHOO.widget.Button({id:"siwiki-menu-tags", label: tagMenuButtonLabel, title:"%%%edit%%%", container:"siwiki-tags", onclick: { fn: onTagMenuButtonClick } });
                                                },
                                                failure:function(o) {
                                                        if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                                //console.log("ajax connection failed");
                                                        }
                                                }
                                   }); 

                                function renderPanel(){                
                                        // create the panel
                                        tagPanel = new YAHOO.widget.Panel("siwiki-panel-tags", {
                                                        draggable: true,
                                                        autofillheight: "body",
                                                        width: "440px",
                                                        height: "320px",
                                                        visible: false,
                                                        close: true,
                                                        underlay: "none",
                                                        xy: [100, 100]
                                                });
                                        tagPanel.setHeader("%%%tagPanelHeader%%%");
                                        tagPanel.setBody("<div id=\"siwiki-tags-panel-layout\"></div>");


                                       // subscribe before the panel renders 
                                        tagPanel.beforeRenderEvent.subscribe(function() {
                                                Event.onAvailable("siwiki-tags-panel-layout", function() {
                                                        // add a layout
                                                        layout = new YAHOO.widget.Layout("siwiki-tags-panel-layout", {
                                                                width: 410,
                                                                height: 250,
                                                                units: [
                                                                        { position: "top", height: 50, body:"", gutter: "2" },
                                                                        { position: "left", width: 150, body: "", gutter: "2", resize: true,scroll: true},
                                                        //              { position: "bottom", height: 30, id: "status", body: "status", gutter: "2",scroll: true },
                                                                        { position: "center", body: "", gutter: "2" }
                                                                ]
                                                        });

                                                        layout.on("render", function(){
                                                        // render add fields
                                                                var t = layout.getUnitByPosition("top");
                                                                var el = document.createElement("span");
                                                                t.body.appendChild(el);
                                                                el.innerHTML = "<label for=\"newTag\">%%%newTagLabel%%%</label><input id=\"siwiki-tag-newtag\" type=\"text\" name=\"newTag\" />";
                                                                var el = document.createElement("span");
                                                                t.body.appendChild(el);
                                                                var add = new YAHOO.widget.Button({id:"siwiki-menu-tag", title:"%%%addTag%%%", container:el, onclick: { fn: addNewTag } });
                                                                
                                                                


                                                        // render tag table
                                                                var l = layout.getUnitByPosition("left");
                                                                var el = document.createElement("div");
                                                                l.body.appendChild(el);
                                                                
                                                                var tagColumns = [
                                                                        {uid: "uid", sortable: true},
                                                                        {key: "tag", label: "%%%tagTableTitle%%%", sortable: true}
                                                                ];

                                                                var tagSource = new YAHOO.util.DataSource(tags);
                                                                tagSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
                                                                tagSource.responseSchema = {
                                                                        fields: ["tag","uid"]
                                                                };

                                                                tagTable = new YAHOO.widget.DataTable(el,tagColumns,tagSource,{selectionMode: "singlecell"});
                                                                tagTable.hideColumn(0);

                                                                var onContextTagTable = function(type, args, table){

                                                                        var task = args[1];
                                                                        if(task) { 
                                                                                var elRow = this.contextEventTarget; 
                                                                                elRow = table.getTrEl(elRow); 

                                                                                if(elRow) { 
                                                                                        switch(task.index) { 
                                                                                                case 0:     // Delete row upon confirmation 
                                                                                                        var oRecord = table.getRecord(elRow); 
                                                                                                                if(confirm("%%%confirmDeleteTag%%% " + oRecord.getData("tag")+"?")) { 
                                                                                                         YAHOO.util.Connect.asyncRequest("POST", "'.$link5->makeUrl(false).'"+oRecord.getData("uid"),
                                                                                                                                   {
                                                                                                                                        success : function(o) {
                                                                                                                                                try {
                                                                                                                                                   var status = YAHOO.lang.JSON.parse(o.responseText);
                                                                                                                                                }
                                                                                                                                                catch (e) {
                                                                                                                                                        //console.log("json parse error: e");
                                                                                                                                                }
                                                                                                                                                if(status){
                                                                                                                                                        table.deleteRow(elRow); 
                                                                                                                                                        var t = tagTable.getRecordSet().getRecords();

                                                                                                                                                        var l = "";
                                                                                                                                                        for(i in t){
                                                                                                                                                                l += t[i].getData().tag + ", ";
                                                                                                                                                        }
                                                                                                                                                        if(l == "") {
                                                                                                                                                                l = "%%%noTag%%%";
                                                                                                                                                                tags = null;
                                                                                                                                                        }
                                                                                                                                                        tagMenuButton.setAttributes({label:l});
                                                                                                                                                }
                                                                                                                                        },
                                                                                                                                        failure:function(o) {
                                                                                                                                                if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                                                                                                                        //console.log("ajax connection failed");
                                                                                                                                                }
                                                                                                                                        }
                                                                                                                           }); 
                                                                                                                } 
                                                                                                } 
                                                                                } 
                                                                        } 
                                                                }
                                                                var contextMenuTagTable = new YAHOO.widget.ContextMenu("siwikitagcontext",
                                                                        {trigger:tagTable.getTbodyEl()});
                                                                contextMenuTagTable.addItem("%%%deleteTag%%%");
                                                                contextMenuTagTable.render("siwiki-tag-context");
                                                                contextMenuTagTable.clickEvent.subscribe(onContextTagTable, tagTable);


                                                                tagTable.subscribe("cellMouseoverEvent",tagTable.onEventHighlightCell);
                                                                tagTable.subscribe("cellMouseoutEvent", tagTable.onEventUnhighlightCell);
                                                                tagTable.subscribe("cellClickEvent", tagTable.onEventSelectCell);
                                                                tagTable.subscribe("cellSelectEvent", renderArticlesHavingTag);

                                                        // render article table
                                                                var l = layout.getUnitByPosition("center");
                                                                var el = document.createElement("div");
                                                                l.body.appendChild(el);
                                                                
                                                                var articleColumns = [
                                                                        {key: "article_uid", sortable: true},
                                                                        {key: "namespace_uid", sortable: true},
                                                                        {key: "article_title", label: "%%%articleTableTitle%%%", sortable: true},
                                                                        {key: "namespace_name", label: "%%%articleTableNamespace%%%", sortable: true}
                                                                ];

                                                                articleSource = new YAHOO.util.DataSource(articles);
                                                                articleSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
                                                                articleSource.responseSchema = {
                                                                        fields: ["article_uid","namespace_uid","article_title", "namespace_name"]
                                                                };

                                                                articleTable = new YAHOO.widget.DataTable(el,articleColumns,articleSource,{selectionMode: "single"});
                                                                articleTable.hideColumn(0);
                                                                articleTable.hideColumn(1);

                                                                articleTable.subscribe("rowMouseoverEvent", articleTable.onEventHighlightRow);
                                                                articleTable.subscribe("rowMouseoutEvent", articleTable.onEventUnhighlightRow);
                                                                articleTable.subscribe("rowClickEvent", articleTable.onEventSelectRow);
                                                                articleTable.subscribe("rowSelectEvent", navigateToArticle);
                                                        });

                                                        layout.render();
                                                });
                                        });
                                        tagPanel.render();

                                        resize = new YAHOO.util.Resize("siwiki-panel-tags", {
                                                handles: ["br"],
                                                autoRatio: true,
                                                status: true,
                                                proxy: true,
                                                useShim: true,
                                                minWidth: 410,
                                                minHeight: 280
                                        });

                                        resize.on("resize", function(args) {
                                                var h = args.height;
                                                var hh = this.header.clientHeight;
                                                var padding = ((10 * 2) + 2);
                                                var bh = (h - hh - padding);
                                                Dom.setStyle(this.body, "height", bh + "px");
                                                layout.set("height", bh);
                                                layout.set("width", (args.width - padding));    
                                                layout.resize();
                                        }, tagPanel, true);
                                }

                                var addNewTag = function(args){
                                        var newTag = Dom.get("siwiki-tag-newtag").value;
                                        newTag = newTag.toLowerCase().replace(/\s+/g,"");
                                        var check = true;
                                        var table = tagTable.getRecordSet().getRecords();

                                        for(i in table){
                                                if(table[i].getData().tag == newTag) check = false;
                                        }

                                        if(check && newTag !== ""){
                                                
                                                YAHOO.util.Connect.asyncRequest("POST", "'.$link4->makeUrl(false).'"+newTag, 
                                                           {
                                                                success : function(o) {
                                                                        try {
                                                                           var status = YAHOO.lang.JSON.parse(o.responseText);
                                                                        }
                                                                        catch (e) {
                                                                                //console.log("json parse error: e");
                                                                        }
                                                                        if(status){
                                                                                data = [{tag: newTag, id: status[0]}];
                                                                                tagTable.addRow(data[0]);
                                                                                var c = tagMenuButton.get("label");
                                                                                if(tags == null){
                                                                                        tags = newTag;
                                                                                        c = newTag + ", ";
                                                                                } else {
                                                                                        c += newTag + ", ";
                                                                                }
                                                                                tagMenuButton.setAttributes({label: c});
                                                                        }
                                                                },
                                                                failure:function(o) {
                                                                        if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                                                //console.log("ajax connection failed");
                                                                        }
                                                                }
                                                   }); 


                                        } else {
                                                if(newTag !== "") alert("%%%tagAlreadyExists%%%");
                                        }
                                }
                                var navigateToArticle = function(args){
                                        var a = articleTable.getSelectedRows();
                                            a = articleTable.getRecord(a[0]).getData();

                                        window.location.href = "'.$link3->makeUrl(false).'&siwiki[namespace]="+a.namespace_uid+"&siwiki[uid]="+a.article_uid+"&siwiki[action]=display";
                                }


                                var renderArticlesHavingTag = function(args){
                                        var tag = tagTable.getSelectedCells();
                                            tag = tagTable.getRecord(tag[0].recordId).getData().tag;
                                        YAHOO.util.Connect.asyncRequest("GET", "'.$link2->makeUrl(false).'&siwiki[tag]="+tag, 
                                                   {
                                                        success : function(o) {
                                                                try {
                                                                   articles = YAHOO.lang.JSON.parse(o.responseText);
                                                                }
                                                                catch (e) {
                                                                        //console.log("json parse error: e");
                                                                }

                                                                articleTable.deleteRows(0,countRows);
                                                                articleTable.addRows(articles,0); 
                                                                countRows = articles.length;
                                                        },
                                                        failure:function(o) {
                                                                if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                                        //console.log("ajax connection failed");
                                                                }
                                                        }
                                        }); 
                                        
                                        
                                }

                        function onTagMenuButtonClick(){
                                tagPanel.show();
                        }       



                        </script>';
        }

        function printBottomToolbar($action){

                //$action must be save or insert
               
                print '<div id="siwiki-bottom-toolbar">
                       <span><label for="siwiki[comment]">%%%comment%%%<input type="text" id="siwiki-article-comment" name="siwiki[comment]" /></label></span> ';
                        $menu .= $this->createSubmitLink($action);
                        $menu .= $this->createCancelLink();

                print $menu;
                print '</div>';
        }

        /**
         *
         * Print the menu items but only IF A USER IS LOGGED IN
         *
         */
    function printMenuItems() {
                $menu = $this->createHomeLink();

                if((tx_siwiki_classes_misc::getUsername()!='' && tx_siwiki_classes_misc::getUsername()!='anonymous') || $this->controller->configurations->get('anonymous')) {
                        // get Edit Link
                        $menu .= $this->createEditLink();
                        $menu .= $this->createVersionLink();
                        $menu .= $this->createDeleteLink();
                        if($this->controller->configurations->get('enableGraphviz')) $menu .= $this->createPlotLink();
                        $menu .= $this->createTocLink();
                        $menu .= $this->createInfoLink();
                        $menu .= $this->createAddLink();
                        if($this->controller->configurations->get('enableFilemanager')) $menu .= $this->createFilemanagerLink();
                        if(!$this->controller->configurations->get('anonymous')) $menu .= $this->createNotificationCheckbox();
                }
                // add a div container to the menu items
                $menu = '<div id="siwiki-menu-items">'.$menu.'</div>';

        print $menu;
        }

        /**
         *
         * Print the menu items but only IF A USER IS LOGGED IN
         *
         */
    function printDiffMenuItems() {
                $menu = $this->createHomeLink();

                if((tx_siwiki_classes_misc::getUsername()!='' && tx_siwiki_classes_misc::getUsername()!='anonymous') || $this->controller->configurations->get('anonymous')) {
                        // get Edit Link
                        $menu .= $this->createBackLink();
                        $menu .= $this->createDropDownBox();
                        $menu .= $this->createRevertLink();
                }
                // add a div container to the menu items
                $menu = '<div id="siwiki-menu-items">'.$menu.'</div>';

        print $menu;
        }

        /**
         *
         * Print the menu items but only IF A USER IS LOGGED IN
         *
         */
    function printEditMenuItems() {

                if((tx_siwiki_classes_misc::getUsername()!='' && tx_siwiki_classes_misc::getUsername()!='anonymous') || $this->controller->configurations->get('anonymous')) {
                        $menu .= $this->createHomeLink();

                        // get Edit Link
                        $menu .= $this->createVersionLink();
                        $menu .= $this->createDeleteLink();
                        if($this->controller->configurations->get('enableGraphviz')) $menu .= $this->createPlotLink();
                        $menu .= $this->createTocLink();
                        $menu .= $this->createInfoLink();
                }
                // add a div container to the menu items
                $menu = '<div id="siwiki-menu-items">'.$menu.'</div>';

        print $menu;
        }

        /**
         *
         * Print the menu items but only IF A USER IS LOGGED IN
         *
         */
    function printNewMenuItems() {
                if((tx_siwiki_classes_misc::getUsername()!='' && tx_siwiki_classes_misc::getUsername()!='anonymous') || $this->controller->configurations->get('anonymous')) {
                        $menu .= $this->createHomeLink();

                        // get Edit Link
              //        $menu .= $this->createVersionLink();
                        if($this->controller->configurations->get('enableGraphviz')) $menu .= $this->createPlotLink();
                        $menu .= $this->createTocLink();
                        $menu .= $this->createInfoLink();
                        // get Edit Link
                }
                // add a div container to the menu items
                $menu = '<div id="siwiki-menu-items">'.$menu.'</div>';

        print $menu;
    }

        /**
         *
         * Creates the HOME Link as YUI Button
         * @return string
         */
        function createHomeLink() {
        $link = tx_div::makeInstance('tx_lib_link');
        $link->designator($this->getDesignator());
        $link->destination($this->getDestination());    
        $link->noHash();
                $link->parameters(array('namespace' => $this->controller->configurations->get('defaultNamespace'),
                                        'uid' => $this->controller->configurations->get('rootpage'),
                                        'action' => 'display'));

                return '<script type="text/javascript">
                        function onButtonClick(){
                                window.location.href = "'.$link->makeUrl(false).'";
                        }       
                       var home = new YAHOO.widget.Button({id:"siwiki-menu-home", title:"%%%home%%%", container:"siwiki-menu-items", onclick: { fn: onButtonClick } });
                       </script>';
    }

        /**
         * Creates the edit link as YUI button
         * @return string
         */
    function createEditLink() {
        $link = tx_div::makeInstance('tx_lib_link');
        $link->designator($this->getDesignator());
        $link->destination($this->getDestination());    
        $link->noHash();
        $link->parameters(array('namespace' => $this->get('namespace'),
                                        'uid' => $this->get('uid'),
                                    'action' => 'edit'));

                return '<script type="text/javascript">
                        function onButtonClick(){
                                window.location.href = "'.$link->makeUrl(false).'";
                        }       
                       var edit = new YAHOO.widget.Button({id:"siwiki-menu-edit", title:"%%%edit%%%", container:"siwiki-menu-items", onclick: { fn: onButtonClick } });
                       </script>';
    }


        /**
         * Creates the back link as YUI button
         * @return string
         */
    function createBackLink() {
        $link = tx_div::makeInstance('tx_lib_link');
        $link->designator($this->getDesignator());
        $link->destination($this->getDestination());    
        $link->noHash();
                $link->parameters(array('namespace' => $this->controller->parameters->get('namespace'),
                                        'uid' => $this->controller->parameters->get('uid'),
                                    'action' => 'display'));

                return '<script type="text/javascript">
                        function onButtonClick(){
                                window.location.href = "'.$link->makeUrl(false).'";
                        }       
                       var back = new YAHOO.widget.Button({id:"siwiki-menu-back", title:"%%%back%%%", container:"siwiki-menu-items", onclick: { fn: onButtonClick } });
                       </script>';
    }

        /**
         * Creates the revert link as YUI button
         *
         * @return string
         */
        function createRevertLink() {
        $link = tx_div::makeInstance('tx_lib_link');
        $link->designator($this->getDesignator());
        $link->destination($this->getDestination());    
        $link->noHash();
                $link->parameters(array('uid' => $this->get('uid'),
                    'action' => 'save'));
                
                return  '<span id="siwiki-panel-revert"></span>
                           <script type="text/javascript">
                               YAHOO.util.Event.onContentReady("siwiki-menu-versions", function () {
                                        var handleYes = function(){
                                               document.siwiki_diff.action = "'.$link->makeUrl(false).'";
                                               document.siwiki_diff.submit();
                                        }                       
                                        var handleNo = function() {
                                               this.cancel();
                                        }
                                        var revertDialog = new YAHOO.widget.SimpleDialog("revert",{ width: "300px",
                                                                        modal: true,
                                                                        fixedcenter: true,
                                                                        visible: false,
                                                                        draggable: false,
                                                                        close: true,
                                                                        icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                                                        text: "%%%revertPanelBody%%%",
                                                                        constraintoviewport: true,
                                                                        buttons: [ { text:"%%%yes%%%", handler:handleYes },
                                                                                   { text:"%%%no%%%", handler:handleNo, isDefault:true }]
                                        });
                                        revertDialog.setHeader("%%%revertPanelHeader%%%");
                                        revertDialog.render("siwiki-panel-revert");

                                        function onButtonClick(){
                                                revertDialog.show();
                                        }       
                                        var revert = new YAHOO.widget.Button({id:"siwiki-menu-revert", title:"%%%revert%%%", container:"siwiki-menu-items", onclick: { fn: onButtonClick } });
                               });       
                       </script>';      
    }

        /**
         * Creates a drop down box
         * @return string
         */
    function createDropDownBox() {
        $link = tx_div::makeInstance('tx_lib_link');
        $link->designator($this->getDesignator());
        $link->destination($this->getDestination());    
        $link->noHash();
        $link->parameters(array('namespace' => $this->controller->parameters->get('namespace'),
                                        'uid' => $this->controller->parameters->get('uid'),
                                        'action' => 'diff',
                                        'newVersion' => $this->controller->parameters->get('newVersion'),
                                        'oldVersion' => ''));
                return '<script type="text/javascript">
                               YAHOO.util.Event.onContentReady("siwiki-menu-items", function () {

                                       var onMenuItemClick = function (p_sType, p_aArgs, p_oItem) {
                                               window.location.href = "'.$link->makeUrl(false).'"+p_oItem.value;
                                       };
                                       var version = '.$this->controller->parameters->get('newVersion').';
                                       var items = [];
                                       var maxItems = 20;
                                       while(version > 1 && maxItems != 0){
                                               version--;
                                               items[version] = { 
                                                        text: "%%%version%%% "+version, 
                                                        value: version, 
                                                        onclick: { fn: onMenuItemClick } 
                                                };
                                                maxItems--;
                                       }
                                        items.reverse();

                                       var dropDownMenu = new YAHOO.widget.Button({ id:"siwiki-menu-versions", type: "menu", label: "%%%version%%% '.$oldVersion.'", name: "versions", menu: items, container: "siwiki-menu-items" });
                               });       
                        </script>';
                       
    }


        /**
         * Creates the version link as YUI button
         * @return string
         */
    function createVersionLink() {
        $link = tx_div::makeInstance('tx_lib_link');
        $link->designator($this->getDesignator());
        $link->destination($this->getDestination());    
        $link->noHash();
        $link->parameters(array('namespace' => $this->get('namespace'),
                                        'uid' => $this->get('uid'),
                                        'oldVersion' => intval($this->get('version'))-1,
                                        'newVersion' => intval($this->get('version')),
                                    'action' => 'diff'));


                return '<script type="text/javascript">
                        function onButtonClick(){
                                window.location.href = "'.$link->makeUrl(false).'";
                        }       
                       var version = new YAHOO.widget.Button({id:"siwiki-menu-version", title:"%%%version%%%", container:"siwiki-menu-items", onclick: { fn: onButtonClick } });
                       </script>';
    }

        /**
         * Creates the delete link as YUI button
         *
         * @return string
         */
        function createDeleteLink() {
        $link = tx_div::makeInstance('tx_lib_link');
        $link->designator($this->getDesignator());
        $link->destination($this->getDestination());    
        $link->noHash();
                $link->parameters(array('namespace' => $this->get('namespace'),
                                        'uid' => $this->get('uid'),
                                        'title' => $this->get('title'),
                    'action' => 'delete'));
                
                return  '<span id="siwiki-panel-delete"></span>
                           <script type="text/javascript">
                                var handleYes = function(){
                                       window.location.href = "'.$link->makeUrl(false).'";  
                                }                       
                                var handleNo = function() {
                                       this.cancel();
                                }
                                var deleteDialog = new YAHOO.widget.SimpleDialog("delete",
                                              { width: "300px",
                                                modal: true,
                                                fixedcenter: true,
                                                visible: false,
                                                draggable: false,
                                                close: true,
                                                icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                                text: "%%%deletePanelBody%%%",
                                                constraintoviewport: true,
                                                buttons: [ { text:"%%%yes%%%", handler:handleYes },
                                                           { text:"%%%no%%%", handler:handleNo, isDefault:true }]
                                               });
                                deleteDialog.setHeader("%%%deletePanelHeader%%%");
                                deleteDialog.render("siwiki-panel-delete");

                                function onButtonClick(){
                                        deleteDialog.show();
                                }       
                                var del = new YAHOO.widget.Button({id:"siwiki-menu-delete", title:"%%%delete%%%", container:"siwiki-menu-items", onclick: { fn: onButtonClick } });
                       </script>';      
    }


        
        /**
         * Creates the plotRelations link
         * @see plotRelations.php
         *
         * @return string
         */
        function createPlotLink() {
        $destination = $GLOBALS['TSFE']->id . ',' . $this->controller->configurations->get('relationsPageType');  
        $link = tx_div::makeInstance('tx_lib_link');
        $link->destination($destination);
        $link->designator($this->getDesignator());
                $link->parameters(array('namespace' => $this->controller->parameters->get('namespace'),
                                        'uid' => $this->controller->parameters->get('uid'),
                                        'action' => 'relations'));
        $link->noHash();
                return '<span id="siwiki-panel-plot"> </span>
                        <script type="text/javascript">
                                        var panel2 = new YAHOO.widget.Panel("panel2", {  visible:false, draggable:true, close:true } );
                                        panel2.setHeader("%%%plot%%%");
                                        panel2.setBody("<img src=\"'.$link->makeUrl(false).'\" alt=\'%%%plot%%%\' />");
                                        panel2.setFooter("%%%plotFooter%%%");
                                        panel2.render("siwiki-panel-plot");

                        function onButtonClick(){
                                panel2.show();
                        }       
                        var plot = new YAHOO.widget.Button({id:"siwiki-menu-plot", title:"%%%plot%%%", container:"siwiki-menu-items", onclick: { fn: onButtonClick } });
                        </script>';      
    }

        /**
         * Creates the index link
         * @see plotRelations.php
         *
         * @return string
         */
        function createTocLink() {
        $destination = $GLOBALS['TSFE']->id . ',' . $this->controller->configurations->get('ajaxPageType');  
        $link = tx_div::makeInstance('tx_lib_link');
        $link->destination($destination);
        $link->designator($this->getDesignator());
                $link->parameters(array('namespace' => $this->controller->parameters->get('namespace'),
                                        'action' => 'toc'));
        $link->noHash();

                return '<span id="siwiki-panel-toc"> </span>
                        <script type="text/javascript">
                                YAHOO.util.Event.onAvailable("siwiki-panel-toc",function(){
                                        var panel3 = new YAHOO.widget.Panel("panel3", {  visible:false, draggable:true, close:true, width: "300px" } );
                                        function onButtonClick(){
                                                YAHOO.util.Connect.asyncRequest("GET", "'.$link->makeUrl(false).'", {
                                                                        success : function(o) {
                                                                                panel3.setHeader("%%%toc%%%");
                                                                                panel3.setBody(o.responseText);
                                                                                panel3.setFooter("%%%tocFooter%%%");
                                                                                panel3.render("siwiki-panel-toc");

                                                                        },
                                                                        failure:function(o) {
                                                                                if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                                                        //console.log("ajax connection failed");
                                                                                }
                                                                        }
                                                }); 
                                                YAHOO.util.Event.onContentReady("panel3",function(){
                                                        panel3.show();
                                                });
                                        }       
                                        var toc = new YAHOO.widget.Button({id:"siwiki-menu-toc", title:"%%%toc%%%", container:"siwiki-menu-items", onclick: { fn: onButtonClick } });
                                });
                        </script>';      
    }


        /**
         * Creates the info link
         *
         * @return string
         */
        function createInfoLink() {
        $destination = $GLOBALS['TSFE']->id . ',' . $this->controller->configurations->get('ajaxPageType');  
        $link = tx_div::makeInstance('tx_lib_link');
        $link->destination($destination);
        $link->designator($this->getDesignator());
                $link->parameters(array('namespace' => $this->controller->parameters->get('namespace'),
                                        'uid' => $this->controller->parameters->get('uid'),
                                        'action' => 'ajax',
                                        'request' => 'getInfo'));
        $link->noHash();

                return '<span id="siwiki-panel-info"></span>
                        <script type="text/javascript">
                        YAHOO.util.Event.onAvailable("siwiki-panel-info", function(){
                                var panel4 = new YAHOO.widget.Panel("panel4", {  visible:false, draggable:true, close:true, width: "300px" } );
                                function onButtonClick(){
                                        YAHOO.util.Connect.asyncRequest("GET", "'.$link->makeUrl(false).'", 
                                                   {
                                                        success : function(o) {
                                                                        try {
                                                                          var messages = YAHOO.lang.JSON.parse(o.responseText);
                                                                        }
                                                                        catch (e) {
                                                                                //console.log("json parse error: e");
                                                                        }
                                                                        var body = new String();
                                                                        for(key in messages) {
                                                                                body += key +" "+messages[key]+"<br />";
                                                                        }
                                                                        panel4.setHeader("%%%info%%%");
                                                                        panel4.setBody(body);
                                                                        panel4.setFooter("%%%infoFooter%%%");
                                                                        panel4.render("siwiki-panel-info");
                                                        },
                                                        failure:function(o) {
                                                                if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                                        //console.log("ajax connection failed");
                                                                }
                                                        }
                                           }); 
                                        YAHOO.util.Event.onContentReady("panel4", function() { 
                                               panel4.show();
                                        });
                                }       
                                var info = new YAHOO.widget.Button({id:"siwiki-menu-info", title:"%%%info%%%", container:"siwiki-menu-items", onclick: { fn: onButtonClick } });
                        });
                        </script>';      
    }


        /**
         * Creates the add link
         *
         * @return string
         */
        function createAddLink() {
        $link = tx_div::makeInstance('tx_lib_link');
        $link->destination($this->getDestination());
        $link->designator($this->getDesignator());
                $link->parameters(array('action' => 'new'));
        $link->noHash();

        $destination = $GLOBALS['TSFE']->id . ',' . $this->controller->configurations->get('ajaxPageType');  
        $linkNs = tx_div::makeInstance('tx_lib_link');
        $linkNs->destination($destination);
        $linkNs->designator($this->getDesignator());
                $linkNs->parameters(array('action' => 'ajax',
                                          'request' => 'getNamespaces'));
        $linkNs->noHash();

                return '<div id="siwiki-panel-add"> 
                                <div class="hd">%%%addHeader%%%</div> 
                                <div class="bd">
                                        <p>%%%addDescription%%%</p>
                                        <form id="dialogForm" name="dialogForm" action="'.$link->makeUrl(false).'" method="GET">                                        
                                                <label for="siwikiTitle">%%%title%%%</label><input type="text" name="siwikiTitle" />  
                                                <input type="hidden" name="siwikiNamespace" id="siwikiNamespace" /><br /><br />
                                                <div id="siwiki-panel-add-loading">
                                                <label for="siwikiNamespace">%%%namespace%%%</label><span id="siwiki-panel-add-namespace"></span> 
                                                </div>
                                        </form>
                                        <br />
                                </div> 
                                <div class="ft">%%%addFooter%%%</div> 
                        </div>

                        <script type="text/javascript">
                                var gotData = false;
                                function createDropDownMenu(namespaces) {
                                        if(!gotData){
                                                var menuItems = [];
                                                for (var i = 0, len = namespaces.length; i < len; ++i) {
                                                    var m = namespaces[i];
                                                    menuItems[i] = {
                                                        text: m.name,
                                                        value: m.uid
                                                    };
                                                }
                                                
                                                var dropDownMenu = new YAHOO.widget.Button({
                                                    type: "menu",
                                                    id: "siwiki-panel-namespace",
                                                    label: menuItems[0].text,
                                                    value: menuItems[0].value,
                                                    name: "namespace",
                                                    menu: menuItems,
                                                    container: "siwiki-panel-add-namespace"
                                                });

                                                var currentNamespace = YAHOO.util.Dom.get("siwikiNamespace");
                                                    currentNamespace.value = menuItems[0].value;

                                                dropDownMenu.getMenu().mouseUpEvent.subscribe(function(ev, args) {
                                                    dropDownMenu.set("label", args[1].cfg.getProperty("text"));
                                                    currentNamespace.value = args[1].value;
                                                    dropDownMenu._hideMenu();
                                                });
                                        }
                                }

                                function onButtonClick(){
                                        if(!gotData){
                                                YAHOO.util.Connect.asyncRequest("GET", "'.$linkNs->makeUrl(false).'", 
                                                {
                                                        success : function(o) {
                                                                        try {
                                                                                var namespaces = YAHOO.lang.JSON.parse(o.responseText);
                                                                        }
                                                                        catch (e){
                                                                        }        
                                                                        createDropDownMenu(namespaces);
                                                                        gotData = true;
                                                         },
                                                        failure:function(o) {
                                                                if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                                }
                                                        }
                                                });
                                        }
                                        panel5.show();
                                } 

                                var panel5 = new YAHOO.widget.Dialog("siwiki-panel-add", 
                                        {       visible:false, 
                                                draggable:false, 
                                                fixedcenter:true, 
                                                modal:true, 
                                                close:true, 
                                                buttons: [ { text:"%%%create%%%", handler:handleSave },
                                                           { text:"%%%cancel%%%", handler:handleCancel, isDefault:true }]
                                        });

                                function handleSave() {
                                       var data = this.getData();
                                       window.location.href = "'.$link->makeUrl(false).'&'.$this->getDesignator().'[namespace]="+data.siwikiNamespace+"&'.$this->getDesignator().'[title]="+data.siwikiTitle.replace(/\s/g,"_");  
                                }

                                function handleCancel() {
                                        this.cancel();
                                }

                                panel5.render();

                                var add = new YAHOO.widget.Button({id:"siwiki-menu-add", title:"%%%add%%%", container:"siwiki-menu-items", onclick: { fn: onButtonClick } });
                        </script>';      
    }

        function createLink($parameters = null, $ajax = true){
                if($ajax) $destination = $GLOBALS['TSFE']->id . ',' . $this->controller->configurations->get('ajaxPageType');  
                else $destination = $this->getDestination();
        $link = tx_div::makeInstance('tx_lib_link');
        $link->destination($destination);
        $link->designator($this->getDesignator());
                $link->parameters($parameters);
        $link->noHash();
                return $link->makeUrl(false);
        }

        /**
         * Creates the filemanager panel
         *
         * @return string
         */
        function createFilemanagerLink() {
        $destination = $GLOBALS['TSFE']->id . ',' . $this->controller->configurations->get('ajaxPageType');  
        $link = tx_div::makeInstance('tx_lib_link');
        $link->destination($destination);
        $link->designator($this->getDesignator());
                $link->parameters(array('action' => 'ajax',
                                        'request' => 'fileUpload'));
        $link->noHash();

        $linkGetFiles = tx_div::makeInstance('tx_lib_link');
        $linkGetFiles->destination($destination);
        $linkGetFiles->designator($this->getDesignator());
                $linkGetFiles->parameters(array('uid' => $this->controller->parameters->get('uid'),
                                                'action' => 'ajax',
                                                'request' => 'getAllFilesByArticle'));
        $linkGetFiles->noHash();

        $linkInitialize = tx_div::makeInstance('tx_lib_link');
        $linkInitialize->destination($destination);
        $linkInitialize->designator($this->getDesignator());
                $linkInitialize->parameters(array('uid' => $this->controller->parameters->get('uid'),
                                                  'action' => 'ajax',
                                                  'request' => 'initializeFilemanager'));
        $linkInitialize->noHash();

                return '<div id="siwiki-panel-filemanager"> 
                                <div class="hd">%%%filemanagerHeader%%%</div> 
                                <div class="bd">
                                        <p><img src="typo3conf/ext/siwiki/resources/images/filemanager32.png" alt="%%%filemanagerHeader%%%" /> %%%filemanagerDescription%%%</p>

                                        <div id="siwiki-panel-filemanager-list">
                                        </div>

                                        <div id="siwiki-panel-filemanager-form">
                                        <h3>%%%filemanagerForm%%%</h3><br />
                                        <form id="filemanagerForm" name="filemanagerForm" action="'.$link->makeUrl(false).'" encoding="multipart/form-data" method="POST">                                        
                                        <label for="file">%%%filemanagerFormFile%%%</label>
                                                <input type="file" id="'.$this->getDesignator().'[file]" name="file" /><br />
                                        <label for="'.$this->getDesignator().'[name]">%%%filemanagerFormName%%%</label>
                                                <input type="text" id="'.$this->getDesignator().'-name" name="'.$this->getDesignator().'[name]" /><br />
                                        <label for="'.$this->getDesignator().'[description]">%%%filemanagerFormDescription%%%</label>
                                                <input type="text" id="'.$this->getDesignator().'[description]" name="'.$this->getDesignator().'[description]" />
                                                <input type="hidden" id="'.$this->getDesignator().'[uid]" name="'.$this->getDesignator().'[uid]" value="'.$this->controller->parameters->get('uid').'" />
                                        </form>
                                        </div>
                                </div> 
                                <div class="ft">%%%filemanagerFooter%%%</div> 
                        </div>
                        <div id="infoDialog"></div> 
                        <script type="text/javascript">
                                        var filemanager;
                                        var checked;
                                        var controlNumber;

                                var loading = new YAHOO.widget.Panel("wait",  
                                        { width:"240px", 
                                        fixedcenter:true, 
                                        close:false, 
                                        draggable:false, 
                                        zindex:4,
                                        modal:true,
                                        visible:false
                                        } 
                                );

                                loading.setHeader("%%%loading%%%");
                                loading.setBody("<img src=\"typo3conf/ext/siwiki/resources/images/loadingBar.gif\" alt=\"%%%loading%%%\" />");
                                loading.render(document.body);

                                var handleInfoDialogOk = function() {
                                        this.hide();
                                }  
                                var infoDialog =  new YAHOO.widget.SimpleDialog("infoDialog", 
                                                { width: "250px",
                                                fixedcenter: true,
                                                visible: false,
                                                draggable: false,
                                                close: true,
                                                modal: true,
                                                constraintoviewport: true,
                                                buttons: [ { text:"%%%ok%%%", handler:handleInfoDialogOk, isDefault:true }]
                                }); 
                                infoDialog.setHeader("%%%error%%%");
                                infoDialog.render();

                                function handleSave() {
                                        this.submit();
                                }

                                function handleCancel() {
                                        this.cancel();
                                }

                                var handleFailure = function(o) {
                                        alert("Submission failed: " + o.status);
                                };

                                var panel6 = new YAHOO.widget.Dialog("siwiki-panel-filemanager", 
                                        {       visible:false, 
                                                draggable:true, 
                                                modal:false, 
                                                close:true, 
                                                buttons: [ { text:"%%%filemanagerFormButton%%%", handler:handleSave },
                                                           { text:"%%%cancel%%%", handler:handleCancel, isDefault:true }]
                                        });
                                panel6.validate = function() {
                                        var file = this.getData().file;
                                        var name = YAHOO.util.Dom.get("siwiki-name").value;
                                        var errorMessage = false;
                                        if(file == ""){
                                               errorMessage = "%%%errorNoFile%%%"; 
                                        } else if(name == ""){
                                                errorMessage = "%%%errorFileName%%%"; 
                                        }
                                        if(errorMessage){
                                                infoDialog.setBody("<img src=\"typo3conf/ext/siwiki/resources/images/error22.png\" alt=\"%%%error%%%\" /> " + errorMessage);
                                                infoDialog.show();
                                                return false;
                                        } else {
                                                loading.show();
                                                return true;
                                        }
                                }

                             var onUpload = function(o) {
                                    var response = YAHOO.lang.JSON.parse(o.responseText);
                                    if (response[0]["status"] !== "UPLOADED")  {
                                                var timer = {
                                                        count: 3,
                                                        "updateLoading" : function(data) {
                                                                this.count--;
                                                                if(this.count == 0){
                                                                        loading.hide();
                                                                        later.cancel();
                                                                        loading.setHeader("%%%loading%%%");
                                                                        loading.setBody("<img src=\"typo3conf/ext/siwiki/resources/images/loadingBar.gif\" alt=\"%%%loading%%%\" />");
                                                                } else {
                                                                        loading.setHeader("%%%error%%%");
                                                                        loading.setBody("<img src=\"typo3conf/ext/siwiki/resources/images/error22.png\" alt=\"%%%error%%%\" /> " + response[0]["status"]);
                                                                }
                                                        }
                                                }
                                        var later = YAHOO.lang.later(1000, timer, "updateLoading",[{"data":"foo"}], true);
                                    } else {
                                                var timer = {
                                                        count: 4,
                                                        "updateLoading" : function(data) {
                                                                this.count--;
                                                                if(this.count == 0){
                                                                        loading.hide();
                                                                        later.cancel();
                                                                        panel6.hide();
                                                                        checked = true;
                                                                        filemanager.set("checked",checked,false); 
                                                                        onFilemanagerClick();
                                                                        loading.setHeader("%%%loading%%%");
                                                                        loading.setBody("<img src=\"typo3conf/ext/siwiki/resources/images/loadingBar.gif\" alt=\"%%%loading%%%\" />");
                                                                } else {
                                                                        loading.setHeader("%%%ok%%%");
                                                                        loading.setBody("<img src=\"typo3conf/ext/siwiki/resources/images/ok22.png\" alt=\"%%%ok%%%\" /> %%%filemanagerUploadOk%%%");
                                                                }
                                                        }
                                                }
                                        var later = YAHOO.lang.later(1000, timer, "updateLoading",[{"data":"foo"}], true);
                                    }
                             }

                             panel6.callback.upload = onUpload;
                        
                                panel6.render();

                                function listFiles(files){
                                        var list = YAHOO.util.Dom.get("siwiki-panel-filemanager-list");
                                        var fileList = "<h3>%%%filemanagerFilelist%%%</h3><br /><table><tr><td>";
                                        var links = [];
                                        var linksDelete = [];

                                                for (var i = 0, len = files.length; i < len; ++i) {
                                                    var m = files[i];
                                                    var urlLink = "'.$this->createLink(Array('uid' => $this->controller->parameters->get('uid')),false).'"; 
                                                    urlLink += "&siwiki[action]=pushfile&siwiki[file]="+m.file_name;

                                                    fileList += "<a href=\""+urlLink+"\" id=\"siwiki-filemanager-link"+i+"\" title=\""+m.file_description+"\">"+m.file_name+"</a>";

                                                    fileList += "<span id=\"linkDelete"+i+"\"></span><br />";

                                                    if(!((i+1)%5)) fileList += "</td><td>";
                                                }
                                                fileList += "</td></tr></table>";
                                                list.innerHTML = fileList;
                                                for (var i = 0, len = files.length; i < len; ++i) {
                                                        if(!links[i]){
                                                                links[i] =  new YAHOO.widget.Button("siwiki-filemanager-link"+i); 

                                                                linksDelete[i] =  new YAHOO.widget.Button({ id: "linkDeleteId"+i, name: files[i].file_name, container: "linkDelete"+i, title: "%%%delete%%%", onclick: { fn: onDeleteClick }}); 
                                                        }
                                                }
                                }

                                function onDeleteClick(ev){
                                        var url = "'.$this->createLink(Array('action' => 'deleteFile', 'uid' => $this->controller->parameters->get('uid'))).'";
                                        url += "&siwiki[fileName]=" + this.get("name");
                                        YAHOO.util.Connect.asyncRequest("GET", url,
                                        {
                                                success : function(o) {
                                                                try {
                                                                    var response = YAHOO.lang.JSON.parse(o.responseText);
                                                                    if (response[0]["status"] !== "DELETED")  {
                                                                            alert(response[0]["status"]);
                                                                    } else {
                                                                            controlNumber--;
                                                                            if(controlNumber == 0){
                                                                                        checked = false;
                                                                                        filemanager.set("checked",checked,false); 
                                                                            } 
                                                                            panel6.hide();
                                                                            onFilemanagerClick();
                                                                    }
                                                                }
                                                                catch (e){
                                                                }        
                                                 },
                                                failure:function(o) {
                                                        if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                        }
                                                }
                                        });
                                }       

                                function onFilemanagerClick(){
                                        if(!gotData){
                                                YAHOO.util.Connect.asyncRequest("GET", "'.$linkGetFiles->makeUrl(false).'", 
                                                {
                                                        success : function(o) {
                                                                        try {
                                                                            var files = YAHOO.lang.JSON.parse(o.responseText);
                                                                        }
                                                                        catch (e){
                                                                        }        
                                                                        listFiles(files);
                                                         },
                                                        failure:function(o) {
                                                                if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                                }
                                                        }
                                                });
                                        }
                                        filemanager.set("checked",checked,false); 
                                        panel6.show();
                                } 



                                        YAHOO.util.Event.onContentReady("siwiki-panel-filemanager", function() {
                                                YAHOO.util.Connect.asyncRequest("GET", "'.$linkInitialize->makeUrl(false).'", 
                                                           {
                                                                success : function(o) {
                                                                                try {
                                                                                  var messages = YAHOO.lang.JSON.parse(o.responseText);
                                                                                }
                                                                                catch (e) {
                                                                                        //console.log("json parse error: e");
                                                                                }
                
                                                                                if(messages.number != 0){
                                                                                        controlNumber = messages.number;
                                                                                        checked = true;
                                                                                } else {
                                                                                        checked = false;
                                                                                } 

                                                                                filemanager = new YAHOO.widget.Button({
                                                                                        type: "checkbox", 
                                                                                        id:"siwiki-menu-filemanager", 
                                                                                        title:"%%%filemanager%%%", 
                                                                                        container:"siwiki-menu-items", 
                                                                                        checked: checked,
                                                                                        onclick: { fn: onFilemanagerClick } 
                                                                                });
                                                                },
                                                                failure:function(o) {
                                                                        if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                                                //console.log("ajax connection failed");
                                                                        }
                                                                }
                                                }); 
                                        }); 
                        </script>';      
    }
        /**
         *
         * Creates the notification checkbox
         *
         * @return string
         */
        function createNotificationCheckbox() {

        $destination = $GLOBALS['TSFE']->id . ',' . $this->controller->configurations->get('ajaxPageType');  
        $link = tx_div::makeInstance('tx_lib_link');
        $link->destination($destination);
        $link->designator($this->getDesignator());
                $link->parameters(array('uid' => $this->controller->parameters->get('uid'),
                                        'action' => 'ajax',
                                        'request' => 'getNotification'));
        $link->noHash();

        $linkSet = tx_div::makeInstance('tx_lib_link');
        $linkSet->destination($destination);
        $linkSet->designator($this->getDesignator());
                $linkSet->parameters(array('uid' => $this->controller->parameters->get('uid'),
                                        'action' => 'ajax',
                                        'request' => 'setNotification'));
        $linkSet->noHash();


                $linkSet = $linkSet->makeUrl(false).'&'.$this->getDesignator().'[mode]=';
                return  '<span id="siwiki-panel-notification"></span>
                           <script type="text/javascript">
                                YAHOO.util.Event.onContentReady("siwiki-menu-info", function() {
                                        var notification;
                                        var checked;
                                        var notificationDialog;
                                        var handleYes = function(){
                                                YAHOO.util.Connect.asyncRequest("GET", "'.$linkSet.'"+checked, 
                                                           {
                                                                success : function(o) {
                                                                },
                                                                failure:function(o) {
                                                                        if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                                                //console.log("ajax connection failed");
                                                                        }
                                                                }
                                                }); 
                                                if(checked){  
                                                        checked = false;
                                                        notificationDialog.setBody("%%%notificationPanelBody1%%%");  
                                                } else {
                                                        checked = true;
                                                        notificationDialog.setBody("%%%notificationPanelBody2%%%");  
                                                }
                                              this.cancel();
                                        }                       
                                        var handleNo = function() {
                                              notification.set("checked",checked,false); 
                                              this.cancel();
                                        }
                                        notificationDialog = new YAHOO.widget.SimpleDialog("notification",{ width: "300px",
                                                                        modal: true,
                                                                        fixedcenter: true,
                                                                        visible: false,
                                                                        draggable: false,
                                                                        close: false,
                                                                        icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                                                                        constraintoviewport: true,
                                                                        buttons: [ { text:"%%%yes%%%", handler:handleYes },
                                                                                   { text:"%%%no%%%", handler:handleNo, isDefault:true }]
                                        });
                                        notificationDialog.setHeader("%%%notificationPanelHeader%%%");
                                        notificationDialog.render("siwiki-panel-notification");

                                        function onCheckBoxClick(){
                                                notificationDialog.show();
                                        }       

                                        YAHOO.util.Event.onContentReady("siwiki-panel-notification", function() {
                                                YAHOO.util.Connect.asyncRequest("GET", "'.$link->makeUrl(false).'", 
                                                           {
                                                                success : function(o) {
                                                                                try {
                                                                                  var messages = YAHOO.lang.JSON.parse(o.responseText);
                                                                                }
                                                                                catch (e) {
                                                                                        //console.log("json parse error: e");
                                                                                }
                
                                                                                if(messages != null){
                                                                                        checked = true;
                                                                                        notificationDialog.setBody("%%%notificationPanelBody2%%%");  
                                                                                } else {
                                                                                        checked = false;
                                                                                        notificationDialog.setBody("%%%notificationPanelBody1%%%");  
                                                                                } 

                                                                                notification = new YAHOO.widget.Button({ 
                                                                                                type: "checkbox", 
                                                                                                title: "%%%notification%%%", 
                                                                                                id: "siwiki-menu-notification", 
                                                                                                value: "1", 
                                                                                                container: "siwiki-menu-items", 
                                                                                                checked: checked,
                                                                                                onclick: { fn: onCheckBoxClick }
                                                                                                });
                                                                },
                                                                failure:function(o) {
                                                                        if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                                                //console.log("ajax connection failed");
                                                                        }
                                                                }
                                                }); 
                                        }); 
                                });
                        </script>';      
    }


        /**
         * Create the cancel link as yui button
         *
         * @return string
         */
        function createCancelLink() {
        $link = tx_div::makeInstance('tx_lib_link');
        $link->designator($this->getDesignator());
        $link->destination($this->getDestination());    
        $link->noHash();
                $link->parameters(array('namespace' => $this->controller->parameters->get('namespace'),
                                        'uid' => $this->controller->parameters->get('uid'),
                                        'action' => 'cancel'));

                return '<script type="text/javascript">
                       YAHOO.util.Event.onContentReady("siwiki_article_container", function() {
                                function onButtonClick(){
                                        window.location.href = "'.$link->makeUrl(false).'";
                                }       
                                var can = new YAHOO.widget.Button({label:"%%%cancel%%%", id:"siwiki-menu-cancel", title:"%%%cancel%%%", container:"siwiki-bottom-toolbar", onclick: { fn: onButtonClick } });
                        });
                       </script>';
    }

        /**
         * Create a submit button with YUI
         *
         */
        function createSubmitLink($action){
                return '<script type="text/javascript">
                       YAHOO.util.Event.onContentReady("siwiki_article_container", function() {
                                var submit = new YAHOO.widget.Button({type:"submit", name:"'.$this->getDesignator().'[action]['.$action.']", label:"%%%save%%%", id:"siwiki-menu-submit", title:"%%%save%%%", container:"siwiki-bottom-toolbar" });
        });
                       </script>';
        }

        /**
         * Print the <form> tag
         */
    function printFormTag($id) {
        $link = tx_div::makeInstance('tx_lib_link');
        $link->destination($this->getDestination());
        $link->noHash();
        $action = $link->makeUrl();
        printf(chr(10) . '<form name="'.$id.'" id="%s" action="%s" method="post">' . chr(10),$id,$action);
    }

        /**
         * Include YUI RTE by printing the <script /> block
         */
    function includeYuiRte($title, $namespace, $articleHash) {

                $title = str_replace("_"," ",$title);
        $destination = $GLOBALS['TSFE']->id . ',' . $this->controller->configurations->get('ajaxPageType');  
        $link = tx_div::makeInstance('tx_lib_link');
        $link->destination($destination);
        $link->designator($this->getDesignator());
                $link->parameters(array('action' => 'ajax',
                                        'request' => 'imageUpload'));
        $link->noHash();

        $linkNs = tx_div::makeInstance('tx_lib_link');
        $linkNs->destination($destination);
        $linkNs->designator($this->getDesignator());
                $linkNs->parameters(array('action' => 'ajax',
                                          'request' => 'getNamespaces'));
        $linkNs->noHash();

        $linkUpdateLocking = tx_div::makeinstance('tx_lib_link');
        $linkUpdateLocking->destination($destination);
        $linkUpdateLocking->designator($this->getDesignator());
                $linkUpdateLocking->parameters(array('uid' => $this->controller->parameters->get('uid'),
                        'action' => 'ajax',
                        'request' => 'updateLocking'));
        $linkUpdateLocking->nohash();

        $linkCancel = tx_div::makeinstance('tx_lib_link');
        $linkCancel->destination($this->getDestination());
        $linkCancel->designator($this->getDesignator());
                $linkCancel->parameters(array('uid' => $this->controller->parameters->get('uid'),
                                                     'action' => 'cancel'));
        $linkCancel->nohash();
                        
        $linksignature = tx_div::makeinstance('tx_lib_link');
        $linksignature->destination($destination);
        $linksignature->designator($this->getDesignator());
                $linksignature->parameters(array('action' => 'ajax',
                                                 'request' => 'getSignature'));
        $linksignature->nohash();
        
                $width = $this->controller->configurations->get('rteWidth');
                $height = $this->controller->configurations->get('rteHeight');
                if(!$width) $width = '600';
                if(!$height) $height = '300';
                
        print "
        <script type='text/javascript'>    
                (function() {
                var Dom = YAHOO.util.Dom,
                    Event = YAHOO.util.Event;
            
                var myConfig = {
                width: '".$width."px',
                    height: '".$height."px',
                handleSubmit: true,
                markup: 'xhtml',
                    animate: true,
                    dompath: true,
                                filterWord: true,
                    focusAtStart: true,
                                css: 'html {height: 95%;}body {height: 100%;padding: 7px; background-color: #fff; font:13px/1.22 arial,helvetica,clean,sans-serif;*font-size:small;*font:x-small;}a {color: blue;text-decoration: underline;cursor: pointer;}.warning-localfile {border-bottom: 1px dashed red !important;}.yui-busy {cursor: wait !important;}img.selected { //Safari image selectionborder: 2px dotted #808080;}img {cursor: pointer !important;border: none;}h2 {font-size: 138.5%;color: #555555;margin-bottom: 8px;border-bottom: 1px solid #aaa;}h3 {font-size: 131%;color: #555555;margin-bottom: 6px;border-bottom: 1px solid #aaa;}h4 {font-size: 123.1%;color: #555555;margin-bottom: 4px;border-bottom: 1px solid #aaa;} .siwiki-article-toc{border: 1px dashed #555555;padding:10px;margin:10px 0;}.siwiki-article-signature { text-align: right; padding: 5px;} table {border-collapse:collapse; }td {border: 1px solid #555;}',
                    toolbar: {
                    collapse: true,
                    titlebar: '".$title." @ ".$namespace."',
                    draggable: false,
                    buttons: [
{ group: 'undoredo', label: '%%%undo%%%/%%%redo%%%', 
buttons: [ 
{ type: 'push', label: '%%%undo%%%', value: 'undo', disabled: true }, 
{ type: 'push', label: '%%%redo%%%', value: 'redo', disabled: true } 
]
                                                 },
                                                { type: 'separator' },
                                                { group: 'fontstyle', label: '%%%typoname%%% %%%typosize%%%',
                                                   buttons: [
                                                   { type: 'select', label: 'Arial', value: 'fontname', disabled: true,
                                                       menu: [
                                                           { text: 'Arial', checked: true },
                                                           { text: 'Times New Roman' },
                                                           { text: 'Courier New' }
                                                       ]
                                                   },
                                                   { type: 'spin', label: '13', value: 'fontsize', range: [ 8, 20 ], disabled: true }
                                                   ]
                                                },
                                                { type: 'separator' },
                                                { group: 'textstyle', label: '%%%typostyle%%%',
                                                   buttons: [
                                                     { type: 'push', label: 'Bold CTRL + SHIFT + B', value: 'bold' },
                                                     { type: 'push', label: 'Italic CTRL + SHIFT + I', value: 'italic' },
                                                     { type: 'push', label: 'Underline CTRL + SHIFT + U', value: 'underline' },
                                                     { type: 'push', label: 'Strike Through', value: 'strikethrough' },
                                                     { type: 'separator' },
                                                     { type: 'color', label: '%%%typocolor%%%', value: 'forecolor', disabled: true },
                                                     { type: 'color', label: '%%%backgroundcolor%%%', value: 'backcolor', disabled: true },
                                                     { type: 'separator' },
                                                     { type: 'push', label: 'Remove Formatting', value: 'removeformat', disabled: true }, 
                                                     { type: 'push', label: 'Show/Hide Hidden Elements', value: 'hiddenelements' } 
                                                   ]
                                                },
                                                { type: 'separator' },
                                                { group: 'indentlist', label: '%%%lists%%%',
                                                    buttons: [
                                                     { type: 'push', label: '%%%listsunorderd%%%', value: 'insertunorderedlist' },
                                                     { type: 'push', label: '%%%listsordered%%%', value: 'insertorderedlist' }
                                                    ]
                                                },
                                                { type: 'separator' },
                                                { group: 'parastyle', label: '%%%paragraph%%%',
                                                    buttons: [
                                                     { type: 'select', label: 'Normal', value: 'heading', disabled: true,
                                                        menu: [
                                                          { text: 'Normal', value: 'none', checked: true },
                                                          { text: 'Header 1', value: 'h2' },
                                                          { text: 'Header 2', value: 'h3' },
                                                          { text: 'Header 3', value: 'h4' }
                                                        ]
                                                     }
                                                    ]
                                                },
                                                { type: 'separator' },
                                                { group: 'insertitem', label: '%%%insertitem%%%',
                                                        buttons: [
                                                            { type: 'push', label: '%%%insertlink%%% CTRL + SHIFT + L', value: 'createlink', disabled: true },
                                                            { type: 'push', label: '%%%insertimage%%%', value: 'insertimage' }
                                                        ]
                                                }    
                    ]
                }
                };
            
                var state = 'off';
            
                var myEditor = new YAHOO.widget.Editor('siwiki_article', myConfig);

                myEditor.on('toolbarLoaded', function() {
                    var codeConfig = {
                        type: 'push', label: 'Edit HTML Code', value: 'editcode'
                    };
                    YAHOO.log('Create the (editcode) Button', 'info', 'example');
                    this.toolbar.addButtonToGroup(codeConfig, 'insertitem');
                    
                    this.toolbar.on('editcodeClick', function() {
                        var ta = this.get('element'),
                            iframe = this.get('iframe').get('element');
            
                        if (state == 'on') {
                            state = 'off';
                            this.toolbar.set('disabled', false);
                            YAHOO.log('Show the Editor', 'info', 'example');
                            YAHOO.log('Inject the HTML from the textarea into the editor', 'info', 'example');
                            this.setEditorHTML(ta.value);
                            if (!this.browser.ie) {
                                this._setDesignMode('on');
                            }
            
                            Dom.removeClass(iframe, 'editor-hidden');
                            Dom.addClass(ta, 'editor-hidden');
                            this.show();
                            this._focusWindow();
                        } else {
                            state = 'on';
                            YAHOO.log('Show the Code Editor', 'info', 'example');
                            this.cleanHTML();
                            YAHOO.log('Save the Editors HTML', 'info', 'example');
                            Dom.addClass(iframe, 'editor-hidden');
                            Dom.removeClass(ta, 'editor-hidden');
                            this.toolbar.set('disabled', true);
                            this.toolbar.getButtonByValue('editcode').set('disabled', false);
                            this.toolbar.selectButton('editcode');
                            this.dompath.innerHTML = 'Editing HTML Code';
                            this.hide();
                        }
                        return false;
                    }, this, true);
            
                    this.on('cleanHTML', function(ev) {
                        YAHOO.log('cleanHTML callback fired..', 'info', 'example');
                        this.get('element').value = ev.html;
                    }, this, true);
                    
                    this.on('afterRender', function() {
                        var wrapper = this.get('editor_wrapper');
                        wrapper.appendChild(this.get('element'));
                        this.setStyle('width', '100%');
                        this.setStyle('height', '100%');
                        this.setStyle('visibility', '');
                        this.setStyle('top', '');
                        this.setStyle('left', '');
                        this.setStyle('position', '');
            
                        this.addClass('editor-hidden');
                    }, this, true);
                }, myEditor, true);

                            myEditor.on('toolbarLoaded', function() {

                                    var tocConfig = {
                                            type: 'push',
                                            label: '%%%toc%%%',
                                            value: 'inserttoc'
                                    }                

                                    myEditor.toolbar.addButtonToGroup(tocConfig,'insertitem');
                                    myEditor.toolbar.on('inserttocClick', function(ev){
                                                this.execCommand('inserthtml','<p class=\'siwiki-article-toc\'><strong>%%%toc%%%</strong><br /> ###TOC### </p>');
                                    }, myEditor, true);";

        if(!$this->controller->configurations->get('anonymous')){

                print "             var signatureConfig = {
                                            type: 'push',
                                            label: '%%%signature%%%',
                                            value: 'insertsignature'
                                    }                

                                    myEditor.toolbar.addButtonToGroup(signatureConfig,'insertitem');
                                    myEditor.toolbar.on('insertsignatureClick', function(ev){
                                                YAHOO.util.Connect.asyncRequest('GET','".$linksignature->makeUrl(false)."', 
                                                {
                                                        success : function(o) {
                                                                try {
                                                                        var signature = YAHOO.lang.JSON.parse(o.responseText);
                                                                } catch(e) {
                                                                       //console.log('json parse error'); 
                                                                }

                                                        myEditor.execCommand('inserthtml','<span class=\'siwiki-article-signature\'><img src=\'typo3conf/ext/siwiki/resources/images/signature22.png\' alt=\'%%%signature%%%\' /> '+signature+' </span>');
                                                        },
                                                        failure:function(o) {
                                                                if(!YAHOO.util.Connect.isCallInProgress(o)) {
                                                                        //console.log('ajax connection failed');
                                                                }
                                                        }
                                                }); 
                                    }, myEditor, true);";
        }                  

        print "             });
                         wikilink(myEditor,'".$linkNs->makeUrl(false)."','".$this->controller->configurations->get("defaultNamespace")."');                
        ";
        if($this->controller->configurations->get('enableImageUpload'))
                print "     yuiImgUploader(myEditor,'".$link->makeUrl(false)."','img');";
        print "
                myEditor.render();
                            updateLocking(myEditor,'".$linkUpdateLocking->makeUrl(false)."','".$linkCancel->makeUrl(false)."','".$this->controller->configurations->get("timeAnArticleRemainsLocked")."','".$articleHash."');
            })();
            </script>";
    }

        /**
         * Print two article versions side by side
         *
         * @param string $old
         * @param string $new
         */
        function printAsDiff($old, $new) {
                $oldArticle = $this->strToArrayByBr($old);
                $newArticle = $this->strToArrayByBr($new);
                $maxLines = (count($newArticle) > count($oldArticle)) ? count($newArticle) : count($oldArticle);
                for($i = 0; $i < $maxLines; $i++) {
                        if(! isset($newArticle[$i])) $newArticle[$i] = ' ';
                        if(! isset($oldArticle[$i])) $oldArticle[$i] = ' ';
                        if($newArticle[$i] != $oldArticle[$i]) {
                                $newArticle[$i] = '<span class="siwiki-diff-difference">'.$newArticle[$i].'</span>';
                        }
                }
                $this->printAsNumberedLines($newArticle);
        }

        /**
         * Print the string with numbered lines
         *
         * @param string $article
         */
        function printAsNumberedLines($article) {
                if(is_array($article)) {
                        $oldArticle = $article;
                } else {
                        $oldArticle = $this->strToArrayByBr($article);
                }
                for($i = 0; $i < count($oldArticle); $i++) {
                        $oldArticle[$i] = '<span class="siwiki-diff-ln">'.($i+1).':</span>'.$oldArticle[$i];
                }
                print $this->arrayToStrWithBr($oldArticle);
        }


        

        /**
         * Print title :)
         *
         * @param string $title
         * @param string $namespace
         */
        function printTitle($title,$namespace, $search = true) {
                $title = str_replace("_"," ",$title);
                $out = '<div id="siwiki-title"><h1>'.$title.' @ '.$namespace.'</h1></div>';
                if($search) $out .= $this->createSearchBox();
                print '<div id="siwiki-titlebar">'.$out.'</div>';
        }

        /**
         * Convert a str to an array
         * @param string $str
         * @return array
         */
        private function strToArrayByBr($str) {
                return preg_split("/<br \/>/", $str);
        }

        /**
         * Assemble the array parts to an string
         *
         * @param array $arr
         * @return string
         */
        private function arrayToStrWithBr($arr) {
                $str = '';
                foreach($arr as $line) {
                        $str .= $line.'<br />';
                }
                return $str;
        }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/siwiki/views/class.tx_siwiki_views_siwiki.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/siwiki/views/class.tx_siwiki_views_siwiki.php']);
}
?>
