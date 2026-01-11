using System.ComponentModel;
using System.ComponentModel.DataAnnotations;

namespace FamilyTreeSystem.Models
{
    /// <summary>
    /// 用户模型
    /// </summary>
    public class User
    {
        /// <summary>
        /// 用户ID主键
        /// </summary>
        [Key]
        [DisplayName("ID")]
        public int Id { get; set; }

        /// <summary>
        /// 用户名，必填，最长50个字符
        /// </summary>
        [Required]
        [StringLength(50)]
        [DisplayName("用户名")]
        public required string Name { get; set; }

        /// <summary>
        /// 电子邮件，必填，最长50个字符
        /// </summary>
        [Required]
        [StringLength(50)]
        [EmailAddress]
        [DisplayName("电子邮件")]
        public required string Email { get; set; }

        /// <summary>
        /// 邮箱验证时间，可空
        /// </summary>
        [DisplayName("邮箱验证时间")]
        public DateTime? EmailVerifiedAt { get; set; }

        /// <summary>
        /// 密码，必填，最长50个字符
        /// </summary>
        [Required]
        [StringLength(50)]
        [DisplayName("密码")]
        public required string Password { get; set; }

        /// <summary>
        /// 用户角色，默认"user"，最长50个字符
        /// </summary>
        [StringLength(50)]
        [DisplayName("用户角色")]
        public string Role { get; set; } = "user";

        /// <summary>
        /// 记住我令牌，可空，最长100个字符
        /// </summary>
        [StringLength(100)]
        [DisplayName("记住我令牌")]
        public string? RememberToken { get; set; }

        /// <summary>
        /// 系统创建时间，默认当前时间
        /// </summary>
        [DisplayName("创建时间")]
        public DateTime CreatedAt { get; set; } = DateTime.Now;
        /// <summary>
        /// 系统更新时间，默认当前时间
        /// </summary>
        [DisplayName("更新时间")]
        public DateTime UpdatedAt { get; set; } = DateTime.Now;

        /// <summary>
        /// 用户创建的家族成员集合导航属性
        /// </summary>
        public ICollection<FamilyMember> FamilyMembers { get; set; } = new List<FamilyMember>();
        /// <summary>
        /// 用户创建的字辈集合导航属性
        /// </summary>
        public ICollection<Ziwei> Ziwei { get; set; } = new List<Ziwei>();
        /// <summary>
        /// 用户创建的宗族集合导航属性
        /// </summary>
        public ICollection<Clan> Clans { get; set; } = new List<Clan>();
        
        
        
        /// <summary>
        /// 用户创建的族规集合导航属性
        /// </summary>
        public ICollection<FamilyRule> FamilyRules { get; set; } = new List<FamilyRule>();
        /// <summary>
        /// 用户创建的宗族活动集合导航属性
        /// </summary>
        public ICollection<ClanActivity> ClanActivities { get; set; } = new List<ClanActivity>();
    }
}