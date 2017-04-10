(function() {

  OCA.Fixity = OCA.Fixity || {};

  /**
   * @namespace
   */
  OCA.Fixity.Util = {

    /**
     * Initialize the Fixity plugin.
     *
     * @param {OCA.Files.FileList} fileList file list to be extended
     */
    attach: function(fileList) {

      if (fileList.id === 'trashbin' || fileList.id === 'files.public') {
        return;
      }

      fileList.registerTabView(new OCA.Fixity.FixityTabView('FixityTabView', {}));

    }
  };
})();

OC.Plugins.register('OCA.Files.FileList', OCA.Fixity.Util);

