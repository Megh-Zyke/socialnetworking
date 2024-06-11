function confirmFriend(user_Id) {
  console.log("Confirm Friend Id " + user_Id);
  var xhr = new XMLHttpRequest();

  var acceptButton = document.getElementById("Friend" + user_Id);
  acceptButton.style.display = "none";

  var confirmMsg = document.querySelector(".dialog-message-" + user_Id);
  confirmMsg.style.color = "rgb(255, 255, 0)";
  confirmMsg.style.margin = "2.5% 0";
  confirmMsg.innerHTML = "Friend Request Accepted";
  xhr.open("POST", "confirm_friends.php", true);
  xhr.setRequestHeader("Content-Type", "application/json");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        console.log(xhr.responseText);
      } else {
        console.error("Failed to add friend : " + xhr.status);
      }
    }
  };

  var data = JSON.stringify({ user_id: user_Id });
  console.log(data);
  xhr.send(data);
}

function deleteFriend(user_Id) {
  console.log("Cancel Friend Id " + user_Id);
  var xhr = new XMLHttpRequest();

  var acceptButton = document.getElementById("Friend" + user_Id);
  acceptButton.style.display = "none";

  var deleteMsg = document.querySelector(".dialog-message-" + user_Id);
  deleteMsg.style.color = "red";
  deleteMsg.style.margin = "2.5% 0";
  deleteMsg.innerHTML = "Friend Request Deleted";
  xhr.open("POST", "delete_friend_request.php", true);
  xhr.setRequestHeader("Content-Type", "application/json");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        console.log(xhr.responseText);
      } else {
        console.error("Failed to add friend : " + xhr.status);
      }
    }
  };

  var data = JSON.stringify({ user_id: user_Id });
  console.log(data);
  xhr.send(data);
}
