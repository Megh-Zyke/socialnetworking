function openPosts() {
  console.log("open");
  document.querySelector(".addPostsPage").style.display = "block";
  document.getElementById("modalOverlay").style.display = "block";
}

function closePage() {
  document.querySelector(".addPostsPage").style.display = "none";
  document.getElementById("modalOverlay").style.display = "none";
}

function confirmDelete() {
  var result = confirm("Are you sure you want to delete this post?");
  return result;
}

function editBio() {
  var bioElement = document.querySelector(".bio textarea");
  bioElement.removeAttribute("readonly");
  bioElement.focus();
  bioElement.selectionStart = bioElement.selectionEnd = bioElement.value.length;
  var bioButton = document.querySelector(".bioEditButton");
  bioButton.textContent = "Save Bio";
  bioButton.onclick = saveBio;
}

function saveBio() {
  var bioElement = document.querySelector(".edit-bio-caption-text");
  var newBio = bioElement.value.trim();

  document.querySelector(".bio").value = newBio;

  const closeBtn = document.querySelector(".close-edit-bio");
  closeBtn.click();

  var xhr = new XMLHttpRequest();
  xhr.open("POST", "update_bio.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        console.log("Bio updated successfully");
      } else {
        console.error("Error updating bio: " + xhr.responseText);
      }
    }
  };
  xhr.send("bio=" + encodeURIComponent(newBio));
}

function editPost(postId) {
  console.log("Editing post with ID: " + postId);
  var postTextElement = document.getElementById("post" + postId);
  console.log();
  var text = postTextElement.textContent;
  if (text === null || text === "") {
    var updatedPostText = prompt("Enter the updated post text:");
  } else {
    var updatedPostText = prompt("Enter the updated post text:", text);
  }

  if (updatedPostText !== null) {
    updatePost(postId, updatedPostText);
  }
}

function updatePost(postId, updatedPostText) {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "update_post.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        console.log("Post updated successfully");
        // Update the post text on the page
        document.querySelector("#postText_" + postId).textContent =
          updatedPostText;
      } else {
        console.error("Error updating post: " + xhr.responseText);
      }
    }
  };
  xhr.send(
    "postId=" + postId + "&postText=" + encodeURIComponent(updatedPostText)
  );
}

function addFriend(user_Id) {
  console.log("Adding Friend Id " + user_Id);
  var xhr = new XMLHttpRequest();

  var acceptButton = document.getElementById(user_Id);
  console.log(acceptButton);

  acceptButton.classList.remove("fa-plus");
  acceptButton.classList.add("fa-check");
  xhr.open("POST", "send_requests.php", true);

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

function confirmFriend(user_Id) {
  console.log("Confirm Friend Id " + user_Id);
  var xhr = new XMLHttpRequest();

  var acceptButton = document.getElementById("Friend" + user_Id);
  acceptButton.style.display = "none";

  var confimButton = document.getElementById("accepted" + user_Id);
  confimButton.style.display = "block";
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

  var confimButton = document.getElementById("reject" + user_Id);
  confimButton.style.display = "block";
}

function addLike(postId) {
  console.log("Adding like to post with ID: " + postId);
  var xhr = new XMLHttpRequest();

  var likeBtn = document.getElementById("like" + postId);
  var color = likeBtn.style.color;

  // Determine if the post is being liked or unliked
  var isLiked = color === "red";

  // Update the color of the like button
  likeBtn.style.color = isLiked ? "black" : "red";

  xhr.open("POST", "add_like.php", true);
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        // Parse the response to get the updated like count
        var response = JSON.parse(xhr.responseText);
        if (response.success) {
          var likeCountElement = document.getElementById("like_count" + postId);
          // Ensure like count doesn't go below zero
          var newLikeCount = Math.max(response.likeCount, 0);
          likeCountElement.textContent = newLikeCount;
        } else {
          console.error("Failed to add like: " + response.message);
        }
      } else {
        console.error("Failed to add like: " + xhr.status);
      }
    }
  };

  var data = JSON.stringify({ post_id: postId });
  xhr.send(data);
}
