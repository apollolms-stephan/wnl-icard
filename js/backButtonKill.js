function onDeviceReady() {
        document.addEventListener("backbutton", backKeyDown, true);
        console.log("PhoneGap is ready");
    }

function backKeyDown() {
        navigator.app.exitApp(); // To exit the app!
}
