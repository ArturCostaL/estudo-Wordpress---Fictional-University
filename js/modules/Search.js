import $ from 'jquery';


class Search {

  //OBJETOS 
  constructor() {
    this.addSearchHTML();
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.searchTerm = $("#search-term");
    this.resultsDiv = $("#search-overlay__results");
    this.typingTime;
    this.previousValue;
    this.isSpinnerVisible = false;
    this.events();
  }

  //EVENTOS
  events() {
  this.openButton.on("click", this.openOverlay.bind(this));
  this.closeButton.on("click", this.closeOverlay.bind(this));

  this.searchTerm.on("keyup", this.typingLogic.bind(this));

  }


  //METODOS

  typingLogic(){

    if (this.searchTerm.val() != this.previousValue) {
      clearTimeout(this.typingTime);
 
      if(this.searchTerm.val()){
        if (!this.isSpinnerVisible) {
          this.resultsDiv.html('<div class="spinner-loader"> </div>');
          this.isSpinnerVisible = true;
          }
        this.typingTime = setTimeout(this.getSearchResults.bind(this), 750);
      }else{
        this.resultsDiv.html('');
        this.isSpinnerVisible = false;
      } 
    }
      this.previousValue = this.searchTerm.val();
  }


  getSearchResults() {
    $.when($.getJSON('http://universidade-ficticia.local/wp-json/wp/v2/posts?search=' + this.searchTerm.val()), $.getJSON('http://universidade-ficticia.local/wp-json/wp/v2/pages?search=' + this.searchTerm.val())).then(
      (posts, pages) => {
        var combinedResults = posts[0].concat(pages[0])
        this.resultsDiv.html(`
        <h2 class="search-overlay__section-title">General Information</h2>
        ${combinedResults.length ? '<ul class="link-list min-list">' : "<p>No general information matches that search.</p>"}
          ${combinedResults.map(item => `<li><a href="${item.link}">${item.title.rendered}</a> ${item.type == "post" ? `by ${item.authorName}` : ""}</li>`).join("")}
        ${combinedResults.length ? "</ul>" : ""}
      `)
        this.isSpinnerVisible = false
      },
      () => {
        this.resultsDiv.html("<p>Unexpected error; please try again.</p>")
      }
    )
  }


  openOverlay(){
    this.searchOverlay.addClass("search-overlay--active");
    $("body").addClass("body-no-scroll");
    setTimeout(() => this.searchTerm.focus(), 301)
    this.searchTerm.val('');
  }

  closeOverlay(){
    this.searchOverlay.removeClass("search-overlay--active");
    $("body").removeClass("body-no-scroll");
  }

  addSearchHTML(){
    $("body").append(`
    <div class="search-overlay">
    <div class="search-overlay__top">
      <div class="container">
        <i class="fa fa-search search-overlay__icon" aria-hidden="true" ></i>
        <input type="text" class="search-term" placeholder="What are you looking for?" id="search-term">
        <i class="fa fa-window-close search-overlay__close" aria-hidden="true" ></i>
      </div>
    </div>
    <div class="container">
      <div id="search-overlay__results">
        
      </div>
    </div>
  </div>
    `)
  }

}

export default Search;