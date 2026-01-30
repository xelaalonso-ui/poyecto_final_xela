package com.example.proyectofinal;

import org.w3c.dom.Comment;

import java.util.List;

public class Post {
    private String id;
    private String userName;
    private String userAvatarUrl;
    private String text;
    private List<String> imageUrls;
    private long timestamp;
    private List<Comment> comments;

    public Post(String id, String userName, String userAvatarUrl, String text,
                List<String> imageUrls, long timestamp, List<Comment> comments) {
        this.id = id;
        this.userName = userName;
        this.userAvatarUrl = userAvatarUrl;
        this.text = text;
        this.imageUrls = imageUrls;
        this.timestamp = timestamp;
        this.comments = comments;
    }

    // Getters
    public String getId() { return id; }
    public String getUserName() { return userName; }
    public String getUserAvatarUrl() { return userAvatarUrl; }
    public String getText() { return text; }
    public List<String> getImageUrls() { return imageUrls; }
    public long getTimestamp() { return timestamp; }
    public List<Comment> getComments() { return comments; }
}