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
        footer.innerText = "Currently playing the song: " + songName;
      } else {
        maudio.src =
          "lib/" + currentArtist + "/" + currentRecord + "/" + songName;
        maudio.play();
        footer.innerText =
          "Now playing: " +
          songName +
          " " +
          "by " +
          currentArtist +
          " from the record: " +
          currentRecord;
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
    })
    .catch((error) => console.error("Error:", error));
}
