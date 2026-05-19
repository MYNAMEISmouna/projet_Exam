
function verifyCode(){

    let email = document.getElementById("email").value;
    let code = document.getElementById("code").value;

    fetch("verify_code.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "email=" + email + "&code=" + code
    })
    .then(res => res.text())
    .then(data => {

        console.log(data); // (optional debug)

        if(data.trim() == "success"){
            document.getElementById("msg").innerHTML = "✔ Compte activé";

            // 🔥 GO TO CREATE PASSWORD PAGE
            window.location.href = "create_password.php";
        }
        else{
            document.getElementById("msg").innerHTML = "❌ Code incorrect";
        }
    });
}