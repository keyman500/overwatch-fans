class Searcher{
 constructor (){
    }

keyhandler(){
    var x = document.getElementById("searchbar");
    var query = x.value;
    console.log("query: ",query);
    this.search(query);
}

async search(query){

var ans = {}

   var q1 = `http://overwatch-fans.lovestoblog.com/wp-json/wp/v2/Trivia?search='${query}'`;
  
      var q2 = `http://overwatch-fans.lovestoblog.com/wp-json/wp/v2/fanfiction?search='${query}'`;

      var q3  = `http://overwatch-fans.lovestoblog.com/wp-json/wp/v2/fanart?search='${query}'`; 

  
    ans.trivia = await this.http(q1);
    ans.fanart = await this.http(q2);
    ans.fanfiction = await this.http(q3);

   console.log(ans);
   this.process_result(ans);

}

    async http(url){
             try{
             let response = await fetch
           (url);
        let result= await response.json();
        return result;
        //process_result(result);
        }catch(e){
          console.log("failed here bro")
          console.log(e);
        }
        
        }

        process_result(ans){
            var results =  [];
            var obj1 ={};
            var obj2 = {};
            var obj3 = {};

        
       
            if(ans.fanart){
           for (let art of ans.fanart) {
           obj1.title = art.title;
           obj1.link= art.link;
           results.push(obj1);
           obj1 = {};
}

            }

            if(ans.trivia){
     for (let tri of ans.trivia) {
           obj2.title = tri.title;
           obj2.link= tri.link;
           results.push(obj2);
           obj2 ={};
}
            }

            if(ans.fanfiction){
     for (let fic of ans.fanfiction){
           obj3.title = fic.title;
           obj3.link= fic.link;
           results.push(obj3);
           obj3= {};
}
            }
            
console.log("results: ",results);
this.format_result(results);
        }

     format_result(results){

       var res = document.getElementById("marc-res");

       res.innerHTML="";

       for(let r of results){
       res.innerHTML+=`<a href='${r.link}'>${r.title.rendered}</a><br/><br/><hr/>`;

       }


     }





}
s = new Searcher();





//https://replit.com/@keyman500/assignment-1#index.html