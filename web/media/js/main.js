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

    swiftApp.Views.EmailEmpty = Backbone.Marionette.ItemView.extend({
        tagName: "tr",
        template: "#swift-table-empty"
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
            modalContentRegion: '#modal-content-region',
            modalHeadersRegion: '#modal-headers-region',
            modalSourceRegion: '#modal-source-region'
        },
        onShow: function() {
            var layoutView = this;
            this.modalContentRegion.on('show', function(view, region){

                var $emailContent = view.$el.find('.email-content').clone();
                view.$el.find('.email-content').remove();

                $iframe = $('<iframe></iframe>').css({
                    border: 'none',
                    width: '100%'
                });

                view.$el.append($iframe);

                $iframe.ready(function(){
                    $iframe[0].contentWindow.document.open();
                    $iframe[0].contentWindow.document.write($emailContent[0].outerHTML);
                    $iframe[0].contentWindow.document.close();

                    setTimeout(function(){
                        $iframe.css('height', $($iframe[0].contentWindow.document).height() + 100);
                    }, 300)
                });

                var $modal = layoutView.$el.find('#modal-layout-view');
                $modal.on('show', function(){
                    $('body').css('overflow', 'hidden')
                });

                $modal.on('hidden', function(){
                    region.reset();
                    $('body').css('overflow', 'auto')
                });

                $modal.modal('show');

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
            this.$el
                .attr('class', 'read-email-btn')
                .attr('data-email', this.model.get('id'));

        },
        onShow: function() {
            if (this.model.get('new')) {
                this.$el.addClass('success');
            }
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
            email.new = 1;
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

    $('body').on('click', '.read-email-btn', function(e){
        e.preventDefault();
        $(this).removeClass('success');
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

        emailModel.set('source_html', emailModel.get('content'));

        var emailContentView = new swiftApp.Views.EmailContentView({
            model: emailModel
        });

        var emailHeadersView = new swiftApp.Views.EmailHeadersView({
            model: emailModel
        });

        modalView.modalContentRegion.show(emailContentView);
        modalView.modalHeadersRegion.show(emailHeadersView);

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


        var emailContentView = new swiftApp.Views.EmailHeadersView({
            model: emailModel
        });

        modalView.modalContentRegion.show(emailContentView);

        return false;
    });

    $('#change-address').on('click', function(){
        var _this = this;
        $(this).attr('disabled', 'disabled');
        $('.active-email').animate({ opacity: 0.2 });

        $.ajax({
            url: '/api/v1/visitors/change_address',
            success: function(res) {
                setTimeout(function(){
                    $(_this).attr('disabled', null);
                }, 3000);

                $('.active-email').animate({ opacity: 1 }).html(res.email + '@swift10minutemail.com', { queue: true });
            }
        });

        return false;
    });

    $('#custom-address').on('click', function(){
        var _this = this;
        vex.defaultOptions.className = 'vex-theme-os';

        vex.dialog.prompt({
            message: 'What email address you wish?',
            placeholder: 'Email prefix Only, no @swift10minutemail required !',
            callback: function(result) {
                if (result) {
                    $.ajax({
                        url: '/api/v1/visitors/change_address/' + result,
                        success: function(res) {
                            setTimeout(function(){
                                $(_this).attr('disabled', null);
                            }, 3000);

                            $('.active-email').animate({ opacity: 1 }).html(res.email + '@swift10minutemail.com', { queue: true });
                        },
                        error: function(res) {
                            setTimeout(function(){
                                vex.dialog.alert(res.responseJSON.error.exception[0].message);
                            }, 400);
                        }
                    });
                }
            }
        });

        return false;
    });
}());