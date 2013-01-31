/**
 * 
 */
function pConfirm(title,content,pid,func){
	 var pop=new Popup({ contentType:3,isReloadOnClose:true,width:300,height:80});
     pop.setContent("title",title);
     pop.setContent("confirmCon",content);
     if(func){
    	 pop.setContent("callBack",func);
     }else{
    	 pop.setContent("callBack",ShowCallBack);
     }
     pop.setContent("parameter",{id:"divCall",obj:pop,pid:pid});
     pop.build();
     pop.show();
}
var pD;
function pDialog(title,url,w,h)
{
	var width=400;
	var height=200;
	if(w){
		width=w;
	}
	if(h){
		height=h;
	}
	pD=new Popup({ contentType:1,isReloadOnClose:false,scrollType:'yes',width:width,height:height});
	pD.setContent("contentUrl",url);
	pD.setContent("title",title);
	pD.build();
	pD.show();
}
function pAlertN(title,content){
	 var pop=new Popup({ contentType:4,isReloadOnClose:false,width:300,height:80,isSupportDraging:false});
    pop.setContent("title",title);
    pop.setContent("alertCon",content);
    pop.build();
    pop.show();
}

function pclose(){
	pD.close();
}