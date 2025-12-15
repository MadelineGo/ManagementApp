using System.ComponentModel.DataAnnotations;
using Microsoft.AspNetCore.Identity.Data;

namespace AuthService.Api.Contracts.Requests;

public class RegisterRequest  
{
    [Required]
    public required string Name { get; init; } 
    
    [Required]
    public required string LastName { get; init; } 
   
    [Required, EmailAddress] 
    public required string Email { get; init; }
    
    [Required]   
    [StringLength(100, MinimumLength = 8, ErrorMessage = "Password must be at least 8 characters long.")]
    public required string Password { get; init; }
}