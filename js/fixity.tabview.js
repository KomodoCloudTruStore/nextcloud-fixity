(function() {

  var FixityTabView = OCA.Files.DetailTabView.extend({

    id: 'fixityTabView',

    className: 'tab fixityTabView',

    /**
     * Returns the tab label
     * 
     * @return {String} label
     */
    getLabel: function() {
      return t('fixity', 'Fixity');
    },

    /**
     * Renders this details view
     *
     * @abstract
     */
    render: function() {
      this._renderSelectList(this.$el);

      this.delegateEvents({
        'change #choose-algorithm': '_onChangeEvent'
      });

    },

    _renderSelectList: function($el) {
      $el.html('<div class="get-fixity">'
        + '<select id="choose-algorithm">'
          + '<option value="">' + t('fixity', 'Choose Algorithm') + '</option>'
          + '<option value="md5">MD5</option>'
          + '<option value="sha256">SHA256</option>'
        + '</select></div>'
      );
    },

    /**
     * Returns whether the current tab is able to display
     * the given file info, for example based on mime type.
     *
     * @param {OCA.Files.FileInfoModel} fileInfo file info model
     * @return {bool} whether to display this tab
     */
    canDisplay: function(fileInfo) {
      if(fileInfo != null) {
        if(!fileInfo.isDirectory()) {
          return true;
        }
      }
      return false;
    },

    /**
     * ajax callback for generating md5 hash
     */
    check: function(fileInfo, algorithmType) {
      // skip call if fileInfo is null
      if(null == fileInfo) {
         _self.updateDisplay({
           response: 'error',
           msg: t('fixity', 'No fileinfo provided.')
         });
         return;
      }

      var url = OC.generateUrl('/apps/fixity/check'),
          data = {source: fileInfo.getFullPath(), type: algorithmType},
          _self = this;

      $.ajax({
        type: 'GET',
        url: url,
        dataType: 'json',
        data: data,
        async: true,
        success: function(data) {
          _self.updateDisplay(data, algorithmType);
        }
      });
    },

    /**
     * display message from ajax callback
     */
    updateDisplay: function(data, algorithmType) {
      var msg = '';

      if('success' == data.response) {
        msg = algorithmType + ': ' + data.msg;
      }

      if('error' == data.response) {
        msg = data.msg;
      }

      msg += '<br><br><a id="reload-fixity" class="icon icon-history" style="display:block" href=""></a>';

      this.delegateEvents({
        'click #reload-fixity': '_onReloadEvent'
      });

      this.$el.find('.get-fixity').html(msg);
    },

    /**
     * changeHandler
     */
    _onChangeEvent: function(ev) {
      var algorithmType = $(ev.currentTarget).val();
      if(algorithmType != '') {
        this.$el.html('<div style="text-align:center; word-wrap:break-word;" class="get-fixity"><p><img src="'
          + OC.imagePath('core','loading.gif')
          + '"><br><br></p><p>'
          + t('fixity', 'Creating Fixity ...')
          + '</p></div>');
        this.check(this.getFileInfo(), algorithmType);
      }
    },

    _onReloadEvent: function(ev) {
      ev.preventDefault();
      this._renderSelectList(this.$el);
      this.delegateEvents({
        'change #choose-algorithm': '_onChangeEvent'
      });
    }

  });

  OCA.Fixity = OCA.Fixity || {};

  OCA.Fixity.FixityTabView = FixityTabView;

})();
