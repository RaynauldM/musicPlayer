let songSelector = document.getElementById("songSelector");
let footer = document.querySelector("footer");
let currentArtist;
let currentRecord;

document.addEventListener("DOMContentLoaded", function () {
  var artists = document.querySelectorAll(".artist");
  artists.forEach(function (artist) {
    artist.addEventListener("click", function () {
      artistId = this.getAttribute("data-artist-id");
      recordId = this.getAttribute("data-record-id"); // Assuming you have record ID in the artist element
      currentArtist = this.innerText;

      showRecords(artistId, recordId);

      songSelector.innerText = "Please choose a record to listen to.";
    });
  });

  nameCleanup("albumSelector", "artist");
  nameCleanup("songSelector", "song", true);

  document
    .getElementById("albumSelector")
    .addEventListener("click", (event) => {
      if (event.target.classList.contains("record")) {
        let recordList = document.getElementsByClassName("record");
        for (el of recordList) {
          el.addEventListener("click", onClick);
        }
      }
    });

  songSelector.addEventListener("click", (event) => {
    if (event.target.classList.contains("song")) {
      let songName = event.target.innerText;
      let maudio = document.getElementById("audio");
      console.log(currentRecord);
      if (!currentArtist) {
        maudio.src = "all_songs/" + songName;
        maudio.play();
        footer.innerText =
          "Currently playing the song: " +
          songName.slice(0, -4).replaceAll("_", " ");
      } else {
        maudio.src =
          "lib/" + currentArtist + "/" + currentRecord + "/" + songName;
        maudio.play();
        footer.innerText =
          "Now playing: " +
          songName.slice(0, -4).replaceAll("_", " ") +
          " " +
          "by " +
          currentArtist.replace("_", " ").toUpperCase() +
          " from the record: " +
          currentRecord.replace("_", " ");
      }
    }
  });
});

function onClick(event) {
  let { id, className } = event.target;
  switch (id) {
    case "backBtn":
      location.reload();
      break;
  }
  switch (className) {
    case "record":
      artistId = this.getAttribute("data-artist-id");
      recordId = this.getAttribute("data-record-id");
      showSongs(artistId, recordId);
      currentRecord = this.innerText;

      break;
  }
}

function createBackBtn() {
  let btn = document.createElement("button");
  btn.id = "backBtn";
  btn.className = "btn invisible";
  btn.innerText = "Back";
  btn.addEventListener("click", onClick);
  return btn;
}

function showRecords(artistId, recordId) {
  var url = "getRecords.php?artistId=" + artistId;
  if (recordId) {
    url += "&recordId=" + recordId;
  }

  fetch(url)
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("albumSelector").innerHTML = data;
      document.getElementById("albumSelector").append(createBackBtn());

      // If a record is clicked, show songs
      if (recordId) {
        showSongs(artistId, recordId);
      }
      nameCleanup("albumSelector", "record");
    })

    .catch((error) => console.error("Error:", error));
}

function showSongs(artistId, recordId) {
  var url = "getSongs.php?artistId=" + artistId;
  if (recordId) {
    url += "&recordId=" + recordId;
  }

  fetch(url)
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("songSelector").innerHTML = data;
      nameCleanup("songSelector", "song", true);
    })
    .catch((error) => console.error("Error:", error));
}

function nameCleanup(contId, btnClass, song = false) {
  let container = document.getElementById(contId);
  let children = container.getElementsByClassName(btnClass);
  for (var i = 0; i < children.length; i++) {
    if (children[i].innerText.includes("_")) {
      children[i].innerText = children[i].innerText.replaceAll("_", " ");
    }
    if (song) {
      children[i].innerText = children[i].innerText.slice(0, -4);
    }
  }
}
