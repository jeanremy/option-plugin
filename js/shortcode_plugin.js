// http://www.wpactions.com/1028/how-to-create-dropdown-list-button-in-tinymce-toolbar/
(function() {
   tinymce.create('tinymce.plugins.columns', {

      init : function(ed, url) {
      },
      createControl : function(n, cm) {

            if(n=='columns'){
                var mlb = cm.createListBox('columns', {
                     title : 'columns',
                     onselect : function(v) {
                        if(tinyMCE.activeEditor.selection.getContent() == ''){
                            tinyMCE.activeEditor.selection.setContent( v )
                        }
                     }
                });
                my_shortcodes = {
                  'full':'[full][/full]'
                  '1/2':'[one-half][/one-half]',
                  '1/3':'[one-third][/one-third]',
                  '2/3':'[two-thirds][/two-thirds]',
                  '1/4':'[one-quarter][/one-quarter]',
                  '3/4':'[three-quarters][/three-quarters]'
                };

                for(var i in my_shortcodes)
                  mlb.add([i],my_shortcodes[i]);

                return mlb;
            }
            return null;
        }


   });
   tinymce.PluginManager.add('columns', tinymce.plugins.columns);
})();




