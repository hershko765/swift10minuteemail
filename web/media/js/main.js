(function(){
    var swiftApp = {
        Views: {},
        Models: {},
        Collections: {},
        totalTime: Server.Visitor.closeDate.timestamp - Server.Visitor.created.timestamp,
        hasTime: true
    };

    var App = new Marionette.Application();

    App.addRegions({
       swiftTableRegion: '#swift-table-region',
       modalRegion: '#modal-region'
    });

    swiftApp.Models.Email = Backbone.Model.extend({
        urlRoot: '/api/v1/emails'
    });

    swiftApp.Collections.Email = Backbone.Collection.extend({
        model: swiftApp.Models.Email,
        url: '/api/v1/emails'
    });

    swiftApp.Views.EmailContentView = Backbone.Marionette.ItemView.extend({
        template: "#email-content"
    });

    swiftApp.Views.EmailHeadersView = Backbone.Marionette.ItemView.extend({
        template: "#email-headers"
    });

    swiftApp.Views.ModalView = Backbone.Marionette.LayoutView.extend({
        template: "#modal-view",
        regions: {
            modalContentRegion: '#modal-content-region'
        },
        onShow: function() {
            var layoutView = this;
            this.modalContentRegion.on('show', function(view, region){
                var $modal = layoutView.$el.find('#modal-layout-view');
                $modal.modal('show');

                $modal.on('hide.bs.modal', function(){
                    region.reset();
                });
            });
        }
    });

    swiftApp.Views.EmailRow = Backbone.Marionette.ItemView.extend({
        tagName: "tr",
        template: "#swift-row",
        templateHelpers: function() {
            var tmp = document.createElement("DIV");
            tmp.innerHTML = this.model.get('content');

            return {
                created_formatted: moment(this.model.get('created')).format('MMMM Do YYYY, h:mm:ss a'),
                content_text: tmp.textContent || tmp.innerText || ""
            }
        },
        onRender: function() {
            if (window.innerWidth <= 991) {
                this.$el
                    .attr('class', 'read-email-btn')
                    .attr('data-email', this.model.get('id'));
            }
            else {
                this.$el
                    .attr('class', null)
                    .attr('data-email', null);
            }
        },
        onShow: function() {
            var _this = this;
            $(window).on('resize.table-resize', function(){
                if (_this.isSmall) {
                    if (window.innerWidth > 991) {
                        _this.isSmall = false;
                        _this.render();
                    };
                }
                else {
                    if (window.innerWidth <= 991) {
                        _this.isSmall = true;
                        _this.render();
                    };
                }
            });
        }
    });

    swiftApp.Views.EmailTable = Marionette.CompositeView.extend({
        childView: swiftApp.Views.EmailRow,
        childViewContainer: "tbody",
        emptyView: swiftApp.Views.EmailEmpty,
        template: "#swift-table"
    });

    var swiftEmailCollection = new swiftApp.Collections.Email();

    swiftEmailCollection.fetch();

    var emailTableView = new swiftApp.Views.EmailTable({
        collection: swiftEmailCollection
    });

    App.swiftTableRegion.show(emailTableView);

    var iosocket = io.connect(Server.streamerHost);

    iosocket.once('connect', function () {

        iosocket.emit('add:visitor', Server.Visitor.id);

        iosocket.on('email:received', function(id, email) {
            swiftEmailCollection.add(email);
        });

        iosocket.on('disconnect', function() {
            // Disconnect Event
        });
    });

    var checkTime = function() {
        var timeLeft = Server.Visitor.closeDate.timestamp - Math.round((new Date()).getTime() / 1000);
        timeLeftPrecentage = 100 / swiftApp.totalTime * timeLeft;

        $('#time-minutes').text(Math.ceil(timeLeft / 60));

        $('#visitor-time-progress').css('width', 100 - timeLeftPrecentage + '%');
        if (timeLeftPrecentage <= 0)
        {
            clearInterval(timeInterval);
            $('#time-minutes').parents('h3:first')
                .text('Your email is deleted, refresh to receive new one !');

            $('#visitor-time-progress').removeClass('progress-bar-success').addClass('progress-bar-danger');
            $('#add_time').attr('disabled', 'disabled');
            swiftApp.hasTime = false;
        }
    };

    checkTime();
    var timeInterval = setInterval(checkTime, 1500);

    $('#add_time').on('click', function(){
        if ( ! swiftApp.hasTime) return;

        var _this = this;
        $.ajax({
            url: '/api/v1/visitors/extend/' + Server.Visitor.id,
            success: function() {
                swiftApp.totalTime += 600;
                Server.Visitor.closeDate.timestamp = Server.Visitor.closeDate.timestamp + 600;

                $(_this).attr('disabled', 'disabled');

                setTimeout(function(){
                    $(_this).attr('disabled', null);
                }, 1000 * 60 * 8);
            }
        });

        return false;
    });

    var modalView = new swiftApp.Views.ModalView();
    App.modalRegion.show(modalView);

    $('body').on('click', '.read-email-btn', function(){
        var emailModel = swiftEmailCollection.get($(this).attr('data-email'));

        var emailContentView = new swiftApp.Views.EmailContentView({
            model: emailModel
        });

        modalView.modalContentRegion.show(emailContentView);

        return false;
    });

    var tagsToReplace = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;'
    };

    function replaceTag(tag) {
        return tagsToReplace[tag] || tag;
    }

    function safe_tags_replace(str) {
        if ( ! str.replace) return str;

        return str.replace(/[&<>]/g, replaceTag);
    }

    $('body').on('click', '.headers-btn', function(){
        var emailModel = swiftEmailCollection.get($(this).attr('data-email'));

        if ( ! emailModel.get('headers_changed')) {
            var headers = JSON.parse(emailModel.get('headers'));

            $.each(headers, function(idx, val){
                val = safe_tags_replace(val);
                headers[idx] = val;
            });

            emailModel.set('headers', headers);
            emailModel.set('headers_changed', 1);
        }

        var emailContentView = new swiftApp.Views.EmailHeadersView({
            model: emailModel
        });

        modalView.modalContentRegion.show(emailContentView);

        return false;
    });
}());