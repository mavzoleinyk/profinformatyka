if ( ! window.console ) console = { log: function(){} };
// The actual plugin
jQuery('#menu-menu').singlePageNav({
    offset: jQuery('#menu-menu').outerHeight(),
    filter: ':not(.external)',
    updateHash: false,
    beforeStart: function() {
        console.log('begin scrolling');
    },
    onComplete: function() {
        console.log('done scrolling');
    }
});