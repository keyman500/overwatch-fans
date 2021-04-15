var container = document.querySelector('.blog-masonry');
var masonry;
imagesLoaded( container, function() {
    masonry = new Masonry( container, {
    // options
    itemSelector: '.blog-item',
    columnWidth: '.grid-sizer',
    percentPosition: true
    });
});