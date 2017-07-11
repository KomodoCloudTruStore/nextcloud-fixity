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

            var container = jQuery('<span></span>');

            var fixityIcon = jQuery('<a><span class="icon icon-settings"></span><span> Fixity</span></a>');
            var spinner = jQuery('<img src="' + OC.imagePath('core','loading.gif') +'">');

			var md5 = jQuery('<button>MD5</button>');
			var sha = jQuery('<button>SHA256</button>');

			md5.css('display', 'none');
			sha.css('display', 'none');
			spinner.css('display', 'none');

			container.append(fixityIcon);
			container.append(spinner);
			container.append(md5);
			container.append(sha);

			container.hover(function () {

				md5.css('display', 'inline');
				sha.css('display', 'inline');

			}, function() {

				md5.css('display', 'none');
				sha.css('display', 'none');

            });

			md5.click(function() {

				var url = OC.generateUrl('/apps/fixity/hashes');

				md5.css('display', 'none');
				sha.css('display', 'none');
				spinner.css('display', 'inline');

				jQuery('tr.selected').each(function() {

					var hash = {

					    'file_id': parseInt(jQuery(this).attr('data-id')),
                        'type': 'md5'

					};

					$.ajax({
						type: 'POST',
						url: url,
						dataType: 'json',
						data: hash,
						async: true,
						tryCount : 0,
    					retryLimit : 3,
						success: function(data) {},
						error: function(xhr, textStatus, errorThrown) {

							if (textStatus == 'timeout') {

								this.tryCount++;

								if (this.tryCount <= this.retryLimit) {

									$.ajax(this);
									return;

								}
								return;

							}

							if (xhr.status == 500) {

								return;

							}

						}
					});


				});

				setInterval(function () {

					spinner.css('display', 'none');

				}, 1000)


            });

			sha.click(function() {

				var url = OC.generateUrl('/apps/fixity/hashes');


				md5.css('display', 'none');
				sha.css('display', 'none');
				spinner.css('display', 'inline');

				jQuery('tr.selected').each(function() {

					var hash = {

						'file_id': parseInt(jQuery(this).attr('data-id')),
						'type': 'sha256'

					};

					$.ajax({
						type: 'POST',
						url: url,
						dataType: 'json',
						data: hash,
						async: true,
						tryCount : 0,
    					retryLimit : 3,
						success: function(data) {},
						error: function(xhr, textStatus, errorThrown) {

							if (textStatus == 'timeout') {

								this.tryCount++;

								if (this.tryCount <= this.retryLimit) {

									$.ajax(this);
									return;

								}
								return;

							}

							if (xhr.status == 500) {

								return;

							}

						}
					});

				});

				setInterval(function () {

					spinner.css('display', 'none');

				}, 1000)

			});

            jQuery('#selectedActionsList').append(container);

            fileList.registerTabView(new OCA.Fixity.FixityTabView('fixityTabView', {}));


        }
    };

})();

OC.Plugins.register('OCA.Files.FileList', OCA.Fixity.Util);