namespace FamilyTreeSystem.Models;

/// <summary>
/// 错误视图模型
/// </summary>
public class ErrorViewModel
{
    public string? RequestId { get; set; }

    public bool ShowRequestId => !string.IsNullOrEmpty(RequestId);
}