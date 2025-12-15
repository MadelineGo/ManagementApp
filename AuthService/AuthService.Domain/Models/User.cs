namespace AuthService.Domain.Models;

public class User
{
    public int Id { get; set; }
    
    public required string Name { get; set; }
    
    public required string LastName { get; set; }
    public required string Username { get; set; }
    public required string Email { get; set; }
    public string Password { get; private set; }
    public DateTimeOffset CreatedAt { get; private set; }
    public DateTimeOffset? LastLogin { get; private set; }
    
    public DateTimeOffset? PreviousLastLogin { get; private set; }
    public bool IsActive { get; set; }
    
    private User() {}
    
    public static User Create(string name, string lastName, string email, string passwordHash)
    {
        if (string.IsNullOrWhiteSpace(email))
            //TODO DomainException
            //throw new DomainException("Email is required.");
            throw new Exception("Email is required.");
        if (string.IsNullOrWhiteSpace(name))
            throw new Exception("name is required.");
        if (string.IsNullOrWhiteSpace(lastName))
            throw new Exception("lastName is required.");
        if (string.IsNullOrWhiteSpace(passwordHash) )
            throw new Exception("Password is required.");


        return new User
        {
            Id = 0,
            Email = email,
            Name = name,
            LastName = lastName,
            Username = name + " " + lastName,
            Password = passwordHash, 
            CreatedAt = DateTime.UtcNow,
            IsActive = true,
            LastLogin = DateTime.UtcNow,
        };
        
    }

    public void Deactivate()
    {
        IsActive = false;
    }
    
    public void UpdateLastLogin()
    {
        this.PreviousLastLogin = this.LastLogin;
        this.LastLogin = DateTime.UtcNow;
    }
}