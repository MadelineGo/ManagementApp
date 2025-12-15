using AuthService.Application.Commands.Register;
using AuthService.Application.Services;
using AuthService.Domain.Models;
using AuthService.Domain.Repositories;
using AuthService.Domain.Services;
using FluentAssertions;
using Moq;
using Xunit;

namespace AuthService.UnitTests.Commands.RegisterUser;

public class RegisterCommandHandlerTests
{
    private readonly Mock<IUserRepository> _userRepositoryMock;
    private readonly Mock<IPasswordService> _passwordServiceMock;
    private readonly Mock<IJwtService> _jwtServiceMock;
    private readonly RegisterCommandHandler _handler;

    public RegisterCommandHandlerTests()
    {
        _userRepositoryMock = new Mock<IUserRepository>();
        _passwordServiceMock = new Mock<IPasswordService>();
        _jwtServiceMock = new Mock<IJwtService>();
        _handler = new RegisterCommandHandler(
            _userRepositoryMock.Object,
            _passwordServiceMock.Object,
            _jwtServiceMock.Object
        );
    }

    [Fact]
    public async Task Handle_ShouldRegisterUser_WhenEmailIsUnique()
    {
        // Arrange
        var command = new RegisterCommand("John", "Doe", "john.doe@example.com", "Password123!");
        
        _userRepositoryMock
            .Setup(x => x.FindByEmailAsync(command.Email))
            .ReturnsAsync((User?)null);
            
        _passwordServiceMock
            .Setup(x => x.CreatePasswordHash(command.Password))
            .Returns("hashed_password");
            
        _jwtServiceMock
            .Setup(x => x.GenerateToken(It.IsAny<int>(), command.Email))
            .Returns("jwt_token");

        // Act
        var result = await _handler.Handle(command, CancellationToken.None);

        // Assert
        result.Should().NotBeNull();
        result.Token.Should().Be("jwt_token");
        result.Email.Should().Be(command.Email);
        
        _userRepositoryMock.Verify(x => x.AddAsync(It.IsAny<User>()), Times.Once);
    }

    [Fact]
    public async Task Handle_ShouldThrowException_WhenUserAlreadyExists()
    {
        // Arrange
        var command = new RegisterCommand("John", "Doe", "john.doe@example.com", "Password123!");
        var existingUser = User.Create("Existing", "User", command.Email, "hash");

        _userRepositoryMock
            .Setup(x => x.FindByEmailAsync(command.Email))
            .ReturnsAsync(existingUser);

        // Act
        Func<Task> act = async () => await _handler.Handle(command, CancellationToken.None);

        // Assert
        await act.Should().ThrowAsync<ApplicationException>()
            .WithMessage("User with this email already exists.");
            
        _userRepositoryMock.Verify(x => x.AddAsync(It.IsAny<User>()), Times.Never);
    }
}
