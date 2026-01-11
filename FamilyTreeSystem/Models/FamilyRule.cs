using System.ComponentModel;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace FamilyTreeSystem.Models
{
    /// <summary>
    /// 家族规矩模型
    /// </summary>
    public class FamilyRule
    {
        /// <summary>
        /// 家族规矩主键ID
        /// </summary>
        [Key]
        [DisplayName("ID")]
        public int Id { get; set; }

        /// <summary>
        /// 家族规矩标题，必填，最长255个字符
        /// </summary>
        [Required]
        [StringLength(255)]
        [DisplayName("标题")]
        public required string Title { get; set; }

        /// <summary>
        /// 家族规矩内容，必填
        /// </summary>
        [Required]
        [DisplayName("内容")]
        public required string Content { get; set; }

        /// <summary>
        /// 家族规矩创建日期，可空
        /// </summary>
        [DisplayName("创建日期")]
        public DateTime? CreatedDate { get; set; }
        /// <summary>
        /// 家族规矩创建者姓名，可空
        /// </summary>
        [DisplayName("创建者")]
        public string? Creator { get; set; } // 创建者姓名 admin

        /// <summary>
        /// 创建该家族规矩的用户ID，必填
        /// </summary>
        [Required]
        [DisplayName("创建人ID")]
        public int UserId { get; set; }
        
        /// <summary>
        /// 家族规矩系统创建时间，默认当前时间
        /// </summary>
        [DisplayName("系统创建时间")]
        public DateTime CreatedAt { get; set; } = DateTime.Now;
        
        /// <summary>
        /// 家族规矩系统更新时间，默认当前时间
        /// </summary>
        [DisplayName("系统更新时间")]
        public DateTime UpdatedAt { get; set; } = DateTime.Now;

        /// <summary>
        /// 创建该家族规矩的用户导航属性
        /// </summary>
        [ForeignKey("UserId")]
        public User? User { get; set; }
    }
}