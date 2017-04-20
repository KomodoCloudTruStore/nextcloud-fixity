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
            this.$el.html('<div class="fixity-details-menu">' +
                '<div class="fixity-details-message"></div>' +
                '<div class="fixity-details-controls"></div>' +
                '</div>'
            );

            this.show(this.getFileInfo());

            this.delegateEvents({
                'change #choose-algorithm': '_onChangeEvent'
            });

        },

        _renderSelectList: function() {

            this.$el.find('.fixity-details-controls').html(
                '<div class="get-fixity">'
                + '<select id="choose-algorithm">'
                + '<option value="">' + t('fixity', 'Choose Algorithm') + '</option>'
                + '<option value="md5">MD5</option>'
                + '<option value="sha256">SHA256</option>'
                + '</select></div>'
                + '<button id="validate-hash">Validate Hashes</button>'
            );

            var _self = this;

            $('#validate-hash').click(function() {

               _self.validate();

            });



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
        show: function(fileInfo) {
            // skip call if fileInfo is null
            if(null == fileInfo) {
                _self.updateDisplay({
                    response: 'error',
                    msg: t('fixity', 'No fileinfo provided.')
                });
                return;
            }

            var url = OC.generateUrl('/apps/fixity/hashes/' + fileInfo['id'] ),
                _self = this;

            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                async: true,
                success: function(data) {
                    _self.updateDisplay({
                        response: 'success',
                        msg: data
                    });
                }
            });
        },

        /**
         * display message from ajax callback
         */
        updateDisplay: function(data) {
            var msg = '';

            if('success' == data.response) {

                if (data.msg.length < 1) {

                    this.$el.find('.fixity-details-message').html("This file has not yet been hashed.");

                } else {


                    var rows = "";

                    for (var i = 0; i < data.msg.length; i++) {

                        var row = "<div class='hash-row' style='padding: 5px 5px 5px 0; overflow-x: scroll;'><p>";

                        row += "<b>" + data.msg[i]['type'] + "</b> ";
                        row += "<i>" + data.msg[i]['timestamp'] + "</i>";

                        row += "</p>";

                        row += "<p>" + data.msg[i]['hash'] + "</p>";


                        rows += row;

                    }

                    rows += '<br>';

                    this.$el.find('.fixity-details-message').html(rows);

                }

                this._renderSelectList();

            }

            if('error' == data.response) {
                msg = data.msg;
            }



            this.delegateEvents({
                'change #choose-algorithm': '_onChangeEvent'
            });

        },

        validate: function() {

            var url = OC.generateUrl('/apps/fixity/hashes/' + this.getFileInfo().id + '/validate' ),
                _self = this;

            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                async: true,
                success: function(data) {

                    if (data) {

                        $(".hash-row").css({'color': 'green'});


                    } else {


                        $(".hash-row").css({'color': 'red'});

                    }

                }

            });

        },

        /**
         * changeHandler
         */
        _onChangeEvent: function(ev) {

            var url = OC.generateUrl('/apps/fixity/hashes');
            var _self = this;

            var hash = { 'file_id': this.getFileInfo().id, 'type': $(ev.currentTarget).val() };

            this.$el.find('.fixity-details-message').html(t('fixity', 'Creating Fixity ...'));

            this.$el.find('.fixity-details-controls').html('<div style="text-align:center; word-wrap:break-word;" class="get-fixity"><p><img src="'
                + OC.imagePath('core','loading.gif')
                + '"><br><br></p>'
                + '</div>');

            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                data: hash,
                async: true,
                success: function(data) {
                    _self.show(_self.getFileInfo());
                }
            });
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